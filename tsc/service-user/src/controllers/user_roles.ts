import { Request, Response, NextFunction } from "express";
import { ReturnHelper } from "../helpers/express/return";
import { OrmHelper } from "../helpers/orm";
import Joi from "joi";
import { ILogObj, Logger } from "tslog";
import { Language } from "../langs/lang";
import { Application, Paging, User, UserRole } from "entity";
import CommonHelper from "../helpers/common";
import { Status } from "entity";
import { RoleList } from "entity";
import * as fastcsv from 'fast-csv';
import dayjs from "dayjs";

const log: Logger<ILogObj> = new Logger({ name: '[UserRoleController]', type: 'pretty' });

export class UserRoleController {
    static async list(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Roles']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter like %name%</li><li>Advance format using field existing {status:\'Y\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
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
                filter: Joi.string().allow('').optional().label('Filter'),
                page: Joi.number().required().min(1).label('Page'),
                limit: Joi.number().required().min(1).label('Limit'),
                with_deleted: Joi.bool().required().label('With Deleted'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: Paging = await schema.validateAsync(req.query);

            const repo = OrmHelper.DB.getRepository(UserRole);

            const offset = (param.page - 1) * param.limit

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: ['name'],
                col_any_like: ['name']
            });

            const res_count = repo.createQueryBuilder()
                .where(whereAttr, whereVal);
            const res_list = repo.createQueryBuilder()
                .where(whereAttr, whereVal)
                .orderBy(param.order_field, param.order_direction)
                .offset(offset)
                .limit(param.limit);

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
        #swagger.tags = ['User Roles']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter like %name%</li><li>Advance format using field existing {status:\'Y\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
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
                filter: Joi.string().allow('').optional().label('Filter'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: Paging = await schema.validateAsync(req.query);

            const repo = OrmHelper.DB.getRepository(UserRole);

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: [],
                col_any_like: ['name']
            });

            const filename = "user_role.csv";

            res.setHeader('Content-Type', 'text/csv');
            res.setHeader('Content-Disposition', 'attachment; filename=' + filename);

            const csvStream = fastcsv.format({
                headers: true,
                writeHeaders: true,
                transform: (row: UserRole): any => ({
                    ...row,
                    created_at: dayjs(row.created_at).format('DD-MM-YYYY HH:MM:ss'),
                })
            });
            csvStream.pipe(res);

            const limit = 50;

            const fetchAndWrite = async (page: number) => {
                const offset = (page - 1) * limit

                const data = await repo.createQueryBuilder()
                    .where(whereAttr, whereVal)
                    .select([
                        'name',
                        'roles',
                        'status',
                        'created_at'])
                    .orderBy(param.order_field, param.order_direction)
                    .offset(offset)
                    .limit(limit).getRawMany();

                if (CommonHelper.countObject(data) === 0) {
                    csvStream.end();
                } else {
                    data.forEach((item: any) => csvStream.write(item));

                    if (CommonHelper.countObject(data) == limit) {
                        fetchAndWrite(page + 1);
                    } else {
                        csvStream.end();
                    }
                }
            };

            fetchAndWrite(1);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 400, 401, Language.lang.failed_view, err.message);
        }
    }

    static async role(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Roles']
        #swagger.parameters['token'] = {
            in: 'query',
            required: true,
            type: 'string'
        }
        */

        try {
            const current_page = 1;
            const total_count_data = CommonHelper.countObject(RoleList);
            const list_data = CommonHelper.objectFlip(RoleList);
            const count_data = CommonHelper.countObject(RoleList);

            return ReturnHelper.successResponselist(res, 200, Language.lang.success_view, count_data, current_page, total_count_data, list_data);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 400, 401, Language.lang.failed_view, err.message);
        }
    }

    static async create(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Roles']
        #swagger.security = [{
            "bearerAuth": []
        }]
         #swagger.requestBody = {
            required: true,
            description: "Fill field roles with id from API master data in api/menu/list",
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user_role"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                name: Joi.string().max(64).required().label('Name'),
                roles: Joi.array().required().label('Roles'),
                status: Joi.string().allow(...Object.values(Status)).required().label('Status'),
                application: Joi.string().required().label('Application'),
            });

            const param: UserRole & { application: string } = await schema.validateAsync(req.body);

            const repo_app = OrmHelper.DB.getRepository(Application);

            const data = new UserRole()
            data.name = param.name
            data.roles = param.roles
            data.status = param.status
            data.application = await repo_app.findOneBy({ id: param.application });

            await OrmHelper.DB.manager.save(data);

            return ReturnHelper.successResponseAny(res, 200, Language.lang.success_insert, data);
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 401, Language.lang.failed_insert, err.message);
        }
    }

    static async update(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Roles']
        #swagger.security = [{
            "bearerAuth": []
        }]
         #swagger.parameters['id'] = {
            in: 'path',
            description: 'User role ID.',
            required: true,
            type: 'string'
        }

        #swagger.requestBody = {
            required: true,
            description: "Fill field roles with id from API master data in api/menu/list",
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user_role"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID'),
                name: Joi.string().max(64).required().label('Name'),
                roles: Joi.array().required().label('Roles'),
                status: Joi.string().allow(...Object.values(Status)).required().label('Status'),
                application: Joi.string().required().label('Application'),
            });

            req.body.id = req.params['id'];

            const param: UserRole & { application: string } = await schema.validateAsync(req.body);

            const repo = OrmHelper.DB.getRepository(UserRole);

            const data = await repo.findOneBy({ id: param.id });

            if (data != null) {
                const repo_app = OrmHelper.DB.getRepository(Application);

                data.name = param.name
                data.roles = param.roles
                data.status = param.status
                data.application = await repo_app.findOneBy({ id: param.application });

                await repo.save(data);

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
        #swagger.tags = ['User Roles']
        #swagger.security = [{
            "bearerAuth": []
        }]
        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User role ID.',
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

            const repo_role = OrmHelper.DB.getRepository(UserRole);
            // const repo_user = OrmHelper.DB.getRepository(User);

            // const check_used = await repo_user.findOneBy({ id_role: param.id })

            // if (check_used == null) {
            const affected = (!param.hard ? await repo_role.softDelete({ id: param.id }) : await repo_role.delete({ id: param.id })).affected;

            if (affected > 0) {
                return ReturnHelper.successResponseAny(res, 200, Language.lang.success_delete, {});
            } else {
                return ReturnHelper.errorResponse(res, 404, 401, Language.lang.failed_not_found, "");
            }
            // } else {
            //     return ReturnHelper.errorResponse(res, 404, 402, Language.lang.failed_related, "");
            // }
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 402, Language.lang.failed_delete, err.message);
        }
    }

    static async restore(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Roles']
        #swagger.security = [{
            "bearerAuth": []
        }]
        #swagger.parameters['id'] = {
            in: 'path',
            description: 'User role ID.',
            required: true,
            type: 'string'
        }
        */
        try {
            const schema = Joi.object().keys({
                id: Joi.string().uuid().required().label('ID')
            });

            const param: UserRole = await schema.validateAsync(req.params);

            const repo = OrmHelper.DB.getRepository(UserRole);

            const affected = (await repo.restore({ id: param.id })).affected;

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