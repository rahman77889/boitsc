import { Response, NextFunction, Application } from "express";
import { Request } from "express-jwt";
import { ReturnHelper } from "../helpers/express/return";
import { OrmHelper } from "../helpers/orm";
import { User, UserRefreshToken, UserRole } from "entity";
import Joi from "joi";
import { ILogObj, Logger } from "tslog";
import { Language } from "../langs/lang";
import { Paging } from "entity";
import CommonHelper from "../helpers/common";
import * as fastcsv from 'fast-csv';
import dayjs from "dayjs";
import exceljs from "exceljs";

const log: Logger<ILogObj> = new Logger({ name: '[UserController]', type: 'pretty' });

export class UserController {
    static async list(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter eq:email or like %name%  or like %username%</li><li>Advance format using field existing {status:\'Y\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
            in: 'query',
            type: 'string'
        }
        #swagger.parameters['limit'] = {
            in: 'query',
            required: true,
            type: 'number'
        }
        #swagger.parameters['page'] = {
            in: 'query',
            required: true,
            type: 'number'
        }
        #swagger.parameters['with_deleted'] = {
            in: 'query',
            required: true,
            type: 'boolean'
        }
        #swagger.parameters['order_field'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        #swagger.parameters['order_direction'] = {
            in: 'query',
            required: true,
            schema: {
                '@enum': ['ASC', 'DESC']
            }
        }
        #swagger.parameters['token'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                filter: Joi.any().allow('').optional().label('Filter'),
                page: Joi.number().required().min(1).label('Page'),
                limit: Joi.number().required().min(1).label('Limit'),
                with_deleted: Joi.bool().required().label('With Deleted'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: Paging = await schema.validateAsync(req.query);

            const userRepository = OrmHelper.DB.getRepository(User);

            const offset = (param.page - 1) * param.limit

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: ['email'],
                col_any_like: ['User.name', 'User.username']
            });

            const res_count = userRepository.createQueryBuilder()
                .where(whereAttr, whereVal);

            const subquery = userRepository
                .createQueryBuilder("User")
                .select("User.id", "id")
                .where(whereAttr, whereVal)
                .orderBy("User." + param.order_field, param.order_direction)
                .offset(offset)
                .limit(param.limit);

            const res_list = userRepository
                .createQueryBuilder("User")
                .innerJoin(
                    "(" + subquery.getQuery() + ")",
                    "sub",
                    "User.id = sub.id"
                )
                .setParameters(subquery.getParameters())
                .leftJoinAndSelect("User.roles", "roles")
                .leftJoinAndSelect("roles.application", "application")
                .orderBy("User." + param.order_field, param.order_direction);

            // const subquery = userRepository
            //     .createQueryBuilder()
            //     .select("User.id")
            //     .where(whereAttr, whereVal)
            //     .orderBy("User." + param.order_field, param.order_direction)
            //     .offset(offset)
            //     .limit(param.limit);

            // const res_list = userRepository
            //     .createQueryBuilder()
            //     .where(`User.id IN (${subquery.getQuery()})`)
            //     .setParameters(subquery.getParameters())
            //     .leftJoinAndSelect("User.roles", "roles")
            //     .leftJoinAndSelect("roles.application", "application");

            // const res_list = userRepository.createQueryBuilder()
            //     .where(whereAttr, whereVal)
            //     .leftJoinAndSelect("User.roles", "roles")
            //     .leftJoinAndSelect("roles.application", "application")
            //     .orderBy(param.order_field, param.order_direction)
            //     .offset(offset)
            //     .limit(param.limit);



            if (param.with_deleted) {
                res_count.withDeleted();
                res_list.withDeleted();
            }

            const current_page = param.page;
            const total_count_data = await res_count.getCount();
            const list_data = await res_list.getMany();
            const count_data = CommonHelper.countObject(list_data);

            return ReturnHelper.successResponselist(res, 200, Language.lang.success_view, count_data, current_page, total_count_data, list_data);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 400, 401, Language.lang.failed_view, err.message);
        }
    }

    static async export(req: Request, res: Response, next: NextFunction): Promise<void | Response> {
        /*
        #swagger.tags = ['User']
        #swagger.parameters['filter'] = {
        description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter eq:email or like %name%  or like %username%</li><li>Advance format using field existing {status:\'Y\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
            in: 'query',
            type: 'string'
        }
        #swagger.parameters['filter'] = {
            in: 'query',
            type: 'string'
        }
        #swagger.parameters['order_field'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        #swagger.parameters['order_direction'] = {
            in: 'query',
            required: true,
            schema: {
                '@enum': ['ASC', 'DESC']
            }
        }
        #swagger.parameters['token'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                filter: Joi.array().allow('').optional().label('Filter'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: Paging = await schema.validateAsync(req.query);

            const userRepository = OrmHelper.DB.getRepository(User);

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: ['email'],
                col_any_like: ['name', 'username']
            });

            const filename = "user.xlsx";

            res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            res.setHeader('Content-Disposition', 'attachment; filename=' + filename);

            const workbook = new exceljs.stream.xlsx.WorkbookWriter({ stream: res });
            const sheet = workbook.addWorksheet('Data');

            sheet.columns = [
                { header: "ID", key: "id", width: 10 },
                { header: "Name", key: "name", width: 20 },
                { header: "Email", key: "email", width: 20 },
                { header: "Username", key: "username", width: 20 },
                { header: "Status", key: "status", width: 10 },
                { header: "Created At", key: "created_at", width: 15 },
                { header: "Roles", key: "role_name", width: 15 },
                { header: "Application", key: "application", width: 15 },
            ];

            const limit = 50;

            const fetchAndWrite = async (page: any) => {
                const offset = (page - 1) * limit

                const data = await userRepository.createQueryBuilder()
                    .where(whereAttr, whereVal)
                    .select([
                        'User.id as id',
                        'User.name as name',
                        'User.email as email',
                        'User.username as username',
                        'User.status as status',
                        'User.created_at as created_at',
                        'roles.name as role_name',
                        'application.id as application'])
                    .leftJoin("User.roles", "roles")
                    .leftJoin("roles.application", "application")
                    .orderBy(param.order_field, param.order_direction)
                    .offset(offset)
                    .limit(limit).getRawMany();

                data.forEach((item: any) => sheet.addRow(item).commit());

                if (CommonHelper.countObject(data) == limit) {
                    fetchAndWrite(page + 1);
                } else {
                    sheet.commit();
                    workbook.commit();
                }
            };

            fetchAndWrite(1);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 400, 401, Language.lang.failed_view, err.message);
        }
    }

    static async create(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
        
        #swagger.requestBody = {
            required: true,
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                email: Joi.string().email().max(64).required().label('Email'),
                username: Joi.string().max(64).required().label('Username'),
                password: Joi.string().pattern(new RegExp('^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$')).min(8).required().label('Password'),
                retype_password: Joi.ref('password'),
                name: Joi.string().max(64).required().label('Name'),
                nik: Joi.string().max(64).optional().allow('').label('NIK'),
                status: Joi.string().required().label('Status'),
            });

            const param: User = await schema.validateAsync(req.body);

            const data = new User()
            data.name = param.name
            data.username = param.username
            data.password = param.password
            data.email = param.email
            data.status = param.status

            if (param.nik && param.nik! + "") {
                data.nik = param.nik
            }

            data.hashPassword();

            await OrmHelper.DB.manager.save(data);

            return ReturnHelper.successResponseAny(res, 200, Language.lang.success_insert, data);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 401, Language.lang.failed_insert, err.message);
        }
    }

    static async addRole(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]

        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }

        #swagger.parameters['id_role'] = {
            in: 'path',
            description: 'User Role ID.',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                id_role: Joi.string().uuid().required().label('ID'),
            });

            const param: { id: string, id_role: string } = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);
            const repo_role = OrmHelper.DB.getRepository(UserRole);

            const data = await userRepository.findOne({
                relations: ['roles', 'roles.application'],
                where: { id: param.id }
            });

            if (data != null) {
                // const new_role = await repo_role.findOneBy({ id: param.id_role });
                const new_role = await repo_role.findOne({
                    relations: {
                        application: true,
                    },
                    where: { id: param.id_role }
                });

                if (data.roles) {
                    for (let r of data.roles) {
                        if (r.id == param.id_role) {
                            //already added
                            return ReturnHelper.errorResponse(res, 409, 401, Language.lang.failed_insert + ", roles already exists ", "");
                        }

                        if (r.application.id == new_role.application.id) {
                            return ReturnHelper.errorResponse(res, 409, 402, Language.lang.failed_insert + ", roles in this application already exists ", "");
                        }
                    }
                }

                if (!data.roles) {
                    data.roles = [];
                }

                data.roles.push(new_role);

                await userRepository.save(data);

                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_update, data);
            } else {
                return ReturnHelper.errorResponse(res, 404, 403, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 404, Language.lang.failed_update, err.message);
        }
    }

    static async deleteRole(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]

        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }

        #swagger.parameters['id_role'] = {
            in: 'path',
            description: 'User Role ID.',
            required: true,
            type: 'string'
        }
        */

        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                id_role: Joi.string().uuid().required().label('ID'),
            });

            const param: { id: string, id_role: string } = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);

            const data = await userRepository.findOne({
                relations: {
                    roles: true,
                },
                where: { id: param.id }
            });

            if (data != null && data.roles) {
                let found = false;
                let new_roles: UserRole[] = [];

                for (let r of data.roles) {
                    if (r.id != param.id_role) {
                        new_roles.push(r);
                    } else if (r.id == param.id_role) {
                        found = true;
                    }
                }

                data.roles = new_roles;

                if (found) {
                    await userRepository.save(data);

                    return ReturnHelper.successResponseAny(res, 200, Language.lang.success_delete, data);
                } else {
                    return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found + ", role not found", "");
                }
            } else {
                return ReturnHelper.errorResponse(res, 404, 402, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 403, Language.lang.failed_delete, err.message);
        }
    }

    static async updateProfile(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
    
         #swagger.requestBody = {
            required: true,
            description: "This action will effect to user related token JWT logged",
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user_profile"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                email: Joi.string().email().max(64).required().label('Email'),
                username: Joi.string().max(64).required().label('Username'),
                name: Joi.string().max(64).required().label('Name'),
            });

            req.body.id = req.auth.data.id;

            const param: User = await schema.validateAsync(req.body);

            const userRepository = OrmHelper.DB.getRepository(User);

            const data = await userRepository.findOneBy({ id: param.id });

            if (data != null) {
                data.name = param.name
                data.username = param.username
                data.email = param.email

                await userRepository.save(data);

                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_update, data);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_update, err.message);
        }
    }

    static async updatePassword(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
    
        #swagger.requestBody = {
            required: true,
            description: "This action will effect to user related token JWT logged",
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
                id: Joi.string().uuid().required().label('ID'),
                password: Joi.string().pattern(new RegExp('^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$')).min(8).required().label('Password'),
                retype_password: Joi.ref('password'),
            });

            req.body.id = req.auth.data.id;

            const param: User = await schema.validateAsync(req.body);

            const userRepository = OrmHelper.DB.getRepository(User);

            const data = await userRepository.findOneBy({ id: param.id });

            if (data != null) {
                if (param.password) {
                    data.password = param.password
                    data.hashPassword();
                }

                await userRepository.save(data);

                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_update, data);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_update, err.message);
        }
    }

    static async update(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
         #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }
    
         #swagger.requestBody = {
            required: true,
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                email: Joi.string().email().max(64).required().label('Email'),
                username: Joi.string().max(64).required().label('Username'),
                password: Joi.string().allow('').optional().label('Password'),
                retype_password: Joi.ref('password'),
                name: Joi.string().max(64).required().label('Name'),
                nik: Joi.string().max(64).optional().allow('').label('NIK'),
                status: Joi.string().required().label('Status'),
            });

            req.body.id = req.params['id'];

            const param: User = await schema.validateAsync(req.body);

            const userRepository = OrmHelper.DB.getRepository(User);

            const data = await userRepository.findOneBy({ id: param.id });

            if (data != null) {
                data.name = param.name
                data.nik = param.nik
                data.username = param.username
                data.email = param.email
                data.status = param.status

                if (param.nik && param.nik != "") {
                    data.nik = param.nik
                }

                if (param.password) {
                    data.password = param.password
                    data.hashPassword();
                }

                await userRepository.save(data);

                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_update, data);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_update, err.message);
        }
    }

    static async delete(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }
        #swagger.parameters['hard'] = {
            in: 'path',
            description: 'Is Hard Delete',
            required: false,
            type: 'boolean'
        }
        */
        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                hard: Joi.bool().optional().allow('').label('Is hard delete?')
            });

            const param: { id: string, hard: boolean } = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);
            const userRTRepository = OrmHelper.DB.getRepository(UserRefreshToken);

            const affected = (!param.hard ? await userRepository.softDelete({ id: param.id }) : await userRepository.delete({ id: param.id })).affected;
            const affected_rt = (!param.hard ? await userRTRepository.softDelete({ id_user: param.id }) : await userRTRepository.delete({ id_user: param.id })).affected;

            if (affected > 0) {
                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_delete, {});
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_delete, err.message);
        }
    }

    static async detail(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }
        #swagger.parameters['token'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        */
        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                token: Joi.string().required().label('Token')
            });

            req.params.token = String(req.query.token) ?? ''

            const param: User & { token: string } = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);

            const detail = await userRepository.findOne({
                relations: ['roles', 'roles.application'],
                where: { id: param.id }
            });

            if (detail != null) {
                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_view, detail);
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_view, err.message);
        }
    }

    static async restore(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User']
        #swagger.security = [{
            "bearerAuth": []
        }]
            
        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User ID.',
            required: true,
            type: 'string'
        }
        */
        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID')
            });

            const param: User = await schema.validateAsync(req.params);

            const userRepository = OrmHelper.DB.getRepository(User);
            const userRTRepository = OrmHelper.DB.getRepository(UserRefreshToken);

            const affected = (await userRepository.restore({ id: param.id })).affected;
            const affected_rt = (await userRTRepository.restore({ id_user: param.id })).affected;

            if (affected > 0) {
                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_restore, {});
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_restore, err.message);
        }
    }
}