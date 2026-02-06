import { Response, NextFunction } from "express";
import { Request } from 'express-jwt'
import { ReturnHelper } from "../helpers/express/return";
import { OrmHelper } from "../helpers/orm";
import Joi from "joi";
import config from "config";
import { ILogObj, Logger } from "tslog";
import { Language } from "../langs/lang";
import { UAParser } from 'ua-parser-js';
import dayjs from 'dayjs'
import jwt from 'jsonwebtoken';
import fs from 'fs';
import { v4 as uuidv4 } from 'uuid';
import { Application, EmailBody, Menu, User, UserRefreshToken, UserRole } from "entity";
import axios from 'axios';
import CommonHelper from "../helpers/common";
import { MoreThan } from "typeorm";

const log: Logger<ILogObj> = new Logger({ name: '[UserController]', type: 'pretty' });

export class AuthController {
    static async login(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['Auth']
        #swagger.requestBody = {
            required: true,
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/login"
                    }  
                }
            }
        } 
        */

        try {
            const schema = Joi.object().keys({
                username: Joi.string().max(64).required().label('Username'),
                password: Joi.string().min(8).required().label('Password'),
                application: Joi.string().required().label('Application'),
            });

            const param: User & { application: string } = await schema.validateAsync(req.body);

            const userRepository = OrmHelper.DB.getRepository(User);
            const userRefreshTokenRepository = OrmHelper.DB.getRepository(UserRefreshToken);

            const user = await userRepository.findOne({
                relations: ['roles', 'roles.application'],
                select: ['id', 'email', 'name', 'nik', 'password', 'username', 'roles'],
                where: { username: param.username },
            })

            if (user != null && user.roles != null && user.roles.length > 0 && user.checkIfPasswordMatch(param.password)) {

                const { browser, device, os } = UAParser(req.get('User-Agent'));

                const refresh_token = new UserRefreshToken();
                refresh_token.id_user = user.id;
                refresh_token.user_agent = req.get('User-Agent');
                refresh_token.remote_addr = req.ip.split(':').pop();
                refresh_token.browser = browser.name + " " + browser.version;
                refresh_token.os = os.name + " " + os.version;
                refresh_token.device = (device.model ?? '') + " " + (device.vendor ?? '');
                refresh_token.platform = device.type ?? 'desktop';
                refresh_token.expired_at = dayjs().add(config.get('auth.refresh_token_lifetime_day'), 'day').toDate();

                await userRefreshTokenRepository.save(refresh_token);

                // const userRoleRepository = OrmHelper.DB.getRepository(UserRole);

                let role_id = '';
                let roles: UserRole;

                if (user.roles) {
                    for (let r of user.roles) {
                        if (r.application.id == param.application) {
                            role_id = r.id;
                            roles = r;
                        }
                    }
                }

                if (role_id == '') {
                    return ReturnHelper.errorResponse(res, 403, 403, Language.lang.failed_access + ', you don\'t have any roles in this application');
                } else {
                    // const userRole = await userRoleRepository.findOneBy({ id: role_id })
                    var hrms = {
                        department_id: null,
                        department_name: null,
                        level_id: null,
                        level_name: null
                    }

                    if (user.nik) {
                        const url = config.get("service.hrms") + "api/get-employee?nik=" + user.nik;
                        const { data: dataHrms } = await axios.get(url);
                        if (dataHrms && dataHrms.status && dataHrms.data.count == 1) {
                            hrms = {
                                department_id: dataHrms.data.list[0].idxdept,
                                department_name: dataHrms.data.list[0].departement,
                                level_id: dataHrms.data.list[0].idxlevel,
                                level_name: dataHrms.data.list[0].level
                            }
                        }
                    }


                    var privateKey = fs.readFileSync('src/helpers/key/private.key');

                    var token = jwt.sign(
                        {
                            exp: Math.floor(Date.now() / 1000) + (Number(config.get('auth.access_token_lifetime'))),
                            data: {
                                id: user.id,
                                username: user.username,
                                name: user.name,
                                id_role: role_id,
                                roles: roles.roles
                            }
                        },
                        privateKey,
                        { algorithm: 'RS256' }
                    );

                    delete user.password;

                    const data = {
                        user: { ...user, ...hrms },
                        role_name: roles.name,
                        roles_list: await AuthController.getListRole(roles.roles),
                        token: {
                            access_token: token,
                            refresh_token: refresh_token.refresh_token
                        }
                    }

                    return ReturnHelper.successResponseAny(res, 200, Language.lang.success_login, data);
                }
            }

            if (user.roles == null || user.roles.length == 0) {
                return ReturnHelper.errorResponse(res, 403, 401, Language.lang.failed_access + ', you don\t have any roles in this application');
            } else {
                return ReturnHelper.errorResponse(res, 403, 402, Language.lang.failed_access);
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 403, Language.lang.failed_access, err.message);
        }
    }

    static async getListRole(roles: any): Promise<any> {
        const repoMenu = OrmHelper.DB.getRepository(Menu);

        const whereAttr = [];
        const whereVal = {};

        for (let k in roles) {
            let p = roles[k];

            whereAttr.push('id = :id' + k);
            whereVal['id' + k] = p;
        }

        const res_list = repoMenu.createQueryBuilder()
            .where('(' + whereAttr.join(' or ') + ') and id_parent is null', whereVal)
            .orderBy('order_number', 'ASC');

        let list_data_final = await res_list.getMany();

        if (list_data_final.length > 0) {
            const res_list_all = await repoMenu.createQueryBuilder()
                .where('(' + whereAttr.join(' or ') + ') and id_parent is not null', whereVal)
                .orderBy('order_number', 'ASC').getMany();

            let list_child: any = {};
            for (let p of res_list_all) {
                if (!list_child[p.id_parent]) {
                    list_child[p.id_parent] = [];
                }

                delete p.status
                delete p.created_at
                delete p.updated_at
                delete p.deleted_at

                list_child[p.id_parent].push(p);
            }

            for (let f of list_data_final) {
                delete f.status
                delete f.created_at
                delete f.updated_at
                delete f.deleted_at

                if (list_child[f.id]) {
                    f.children = list_child[f.id];

                    for (let g of f.children) {
                        if (list_child[g.id]) {
                            g.children = list_child[g.id];

                            for (let h of g.children) {
                                if (list_child[h.id]) {
                                    h.children = list_child[h.id];

                                    for (let i of h.children) {
                                        if (list_child[i.id]) {
                                            i.children = list_child[i.id];

                                            for (let j of i.children) {
                                                if (list_child[j.id]) {
                                                    j.children = list_child[j.id];

                                                    for (let k of j.children) {
                                                        if (list_child[k.id]) {
                                                            k.children = list_child[k.id];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return list_data_final;

        }

        return {};
    }

    static async logout(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['Auth']
         #swagger.parameters['refresh_token'] = {
            in: 'path',
            description: 'Refresh Token',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                refresh_token: Joi.string().uuid().required().label('Refresh Token'),
            });

            const param: UserRefreshToken = await schema.validateAsync(req.params);

            const repo = OrmHelper.DB.getRepository(UserRefreshToken);

            const affected = (await repo.delete({ refresh_token: param.refresh_token })).affected;

            if (affected > 0) {
                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_logout);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_logout, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_logout, err.message);
        }
    }

    static async resetPassword(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['Auth']
        #swagger.parameters['email'] = {
            in: 'path',
            description: 'Email',
            required: true,
            type: 'string'
        }
        #swagger.parameters['application'] = {
            in: 'path',
            description: 'Application',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                email: Joi.string().email().required().label('Email'),
                application: Joi.string().required().label('Application'),
            });

            const param: { email: string, application: string } = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);

            const user = await userRepository.findOne({
                where: { email: param.email },
            })

            user.reset_token = uuidv4();

            await userRepository.save(user);

            //send email reset password
            const url = config.get("service.notification") + "api/email/send";

            // console.log(url, 'url')

            const template_raw = fs.readFileSync('assets/reset_password.html').toString();

            const appRepository = OrmHelper.DB.getRepository(Application);
            const app = await appRepository.findOneBy({ id: param.application })


            const pairMap = {
                // 'LINK': config.get('auth.reset_password_url') + user.reset_token,
                'LINK': app.reset_password_url + user.reset_token,
                'NAME': user.name
            }

            const template = CommonHelper.generateTemplate(template_raw, pairMap);

            const email: EmailBody = {
                subject: "Reset Password",
                to: [user.email],
                cc: [],
                bcc: [],
                html: template
            }

            const { data } = await axios.post(url, email);

            return ReturnHelper.successResponseAny(res, 200, Language.lang.success, data);


            // return ReturnHelper.errorResponse(res, 403, 401, Language.lang.failed_access);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed, err.message);
        }
    }

    static async renewToken(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['Auth']
        #swagger.parameters['refresh_token'] = {
            in: 'path',
            description: 'Refresh Token',
            required: true,
            type: 'string'
        }
        #swagger.parameters['application'] = {
            in: 'path',
            description: 'Application',
            required: true,
            type: 'string'
        }
        */
        try {
            const schema = Joi.object().keys({
                refresh_token: Joi.string().uuid().required().label('Refresh Token'),
                application: Joi.string().required().label('Application'),
            });

            const param: UserRefreshToken & { application: string } = await schema.validateAsync(req.params);

            const userRefreshTokenRepo = OrmHelper.DB.getRepository(UserRefreshToken);

            const refresh_token = await userRefreshTokenRepo.findOneBy({ refresh_token: param.refresh_token, expired_at: MoreThan(new Date()) });

            if (refresh_token != null) {
                const userRepo = OrmHelper.DB.getRepository(User);

                const user = await userRepo.findOne({ relations: ['roles', 'roles.application'], where: { id: refresh_token.id_user } });

                let role_id = '';
                let roles: UserRole;

                if (user.roles != null && user.roles.length > 0) {
                    for (let r of user.roles) {
                        if (r.application.id == param.application) {
                            role_id = r.id;

                            roles = r;
                        }
                    }
                } else {
                    return ReturnHelper.errorResponse(res, 403, 401, Language.lang.failed_access + ', you don\'t have any roles in this application');
                }

                // const userRoleRepository = OrmHelper.DB.getRepository(UserRole);
                // const userRole = await userRoleRepository.findOneBy({ id: role_id });

                if (roles) {
                    var privateKey = fs.readFileSync('src/helpers/key/private.key');

                    var token = jwt.sign(
                        {
                            exp: Math.floor(Date.now() / 1000) + (Number(config.get('auth.access_token_lifetime'))),
                            data: {
                                id: user.id,
                                username: user.username,
                                name: user.name,
                                id_role: role_id,
                                roles: roles.roles
                            }
                        },
                        privateKey,
                        { algorithm: 'RS256' }
                    );

                    return ReturnHelper.successResponseAny(res, 200, Language.lang.success, {
                        token,
                        role_name: roles.name,
                        roles_list: await AuthController.getListRole(roles.roles)
                    });
                } else {
                    return ReturnHelper.errorResponse(res, 403, 402, Language.lang.failed_access + ', you don\'t have any roles in this application');
                }
            } else {
                return ReturnHelper.errorResponse(res, 404, 403, Language.lang.failed, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 404, Language.lang.failed, err.message);
        }
    }

    static async updatePassword(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['Auth']
    
        #swagger.parameters['token'] = {
            in: 'path',
            description: 'Token',
            required: true,
            type: 'string'
        }
    
        #swagger.requestBody = {
            required: true,
            description: "This action will effect to user related token",
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user_password"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                reset_token: Joi.string().uuid().required().label('Token'),
                password: Joi.string().required().label('Password'),
                retype_password: Joi.ref('password'),
            });

            req.body.reset_token = req.params.token ?? '';

            const param: User = await schema.validateAsync(req.body);

            const userRepository = OrmHelper.DB.getRepository(User);

            const data = await userRepository.findOneBy({ reset_token: param.reset_token });

            if (data != null) {
                data.password = param.password;
                data.reset_token = null;
                data.hashPassword();

                await userRepository.save(data);

                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_update);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_update, err.message);
        }
    }
}