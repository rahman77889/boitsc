import { Response, NextFunction } from "express";
import { Request } from "express-jwt";
import { ReturnHelper } from "../helpers/express/return";
import { OrmHelper } from "../helpers/orm";
import Joi from "joi";
import { ILogObj, Logger } from "tslog";
import { Language } from "../langs/lang";
import { ActivityTypeReverse, Application, Paging, User } from "entity";
import CommonHelper from "../helpers/common";
import { ActivityType } from "entity";
import * as fastcsv from 'fast-csv';
import dayjs from "dayjs";
import { UserActivity } from "entity";
import exceljs from "exceljs";

const log: Logger<ILogObj> = new Logger({ name: '[UserActivityController]', type: 'pretty' });

interface PagingActivity extends Paging {
    all_user: boolean
}

export class UserActivityController {
    static async list(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Activity']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter like %module% or like %description%</li><li>Advance format using field existing {action:\'C\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
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
        #swagger.parameters['all_user'] = {
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
                all_user: Joi.bool().optional().allow('').label('All User'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: PagingActivity = await schema.validateAsync(req.query);

            const userRepository = OrmHelper.DB.getRepository(UserActivity);

            const offset = (param.page - 1) * param.limit

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: [],
                col_any_like: ['u.username', 'module', 'description'],
                additional_where: !param.all_user ? 'id_user = :id_user' : ''
            });

            if (!param.all_user) {
                whereVal['id_user'] = req.auth.data.id;
            }

            const res_count = userRepository.createQueryBuilder('user_activity')
                .leftJoinAndSelect("user_activity.application", "application")
                .leftJoinAndSelect("user_activity.user", "user")
                .where(whereAttr, whereVal);
            const res_list = userRepository.createQueryBuilder('user_activity')
                .leftJoinAndSelect("user_activity.application", "application")
                .leftJoinAndSelect("user_activity.user", "user")
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
            const list_data = await res_list.getMany()
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
        #swagger.tags = ['User Activity']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter like %module% or like %description%</li><li>Advance format using field existing {action:\'C\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
            in: 'query',
            type: 'string'
        }
        #swagger.parameters['filter'] = {
            in: 'query',
            type: 'string'
        }
        #swagger.parameters['all_user'] = {
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
                all_user: Joi.bool().required().label('All User'),
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: PagingActivity = await schema.validateAsync(req.query);

            const repo = OrmHelper.DB.getRepository(UserActivity);

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: [],
                col_any_like: ['module', 'description'],
                additional_where: !param.all_user ? 'id_user = :id_user' : ''
            });

            if (!param.all_user) {
                whereVal['id_user'] = req.auth.data.id;
            }

            const filename = "user_activity.xlsx";
            res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            res.setHeader('Content-Disposition', 'attachment; filename=' + filename);

            const workbook = new exceljs.stream.xlsx.WorkbookWriter({ stream: res });
            const sheet = workbook.addWorksheet('Data');

            sheet.columns = [
                { header: "Module", key: "module", width: 15 },
                { header: "Description", key: "description", width: 25 },
                { header: "Action", key: "action", width: 10 },
                { header: "Url", key: "url", width: 25 },
                { header: "Created At", key: "created_at", width: 15 },
                { header: "By", key: "by", width: 20 }
            ];

            const limit = 50;

            const fetchAndWrite = async (page: number) => {
                const offset = (page - 1) * limit

                // const data = await repo.createQueryBuilder()
                //     .where(whereAttr, whereVal)
                //     .select([
                //         'module',
                //         'description',
                //         'action',
                //         'url',
                //         'created_at'])
                //     .addSelect('(SELECT u.username FROM users u WHERE u.id = id_user)', 'username')
                //     .orderBy(param.order_field, param.order_direction)
                //     .offset(offset)
                //     .limit(limit).getRawMany();

                const data = await repo.createQueryBuilder('user_activity')
                    .leftJoinAndSelect("user_activity.application", "application")
                    .leftJoinAndSelect("user_activity.user", "user")
                    .where(whereAttr, whereVal)
                    .orderBy(param.order_field, param.order_direction)
                    .offset(offset)
                    .limit(param.limit).getMany();

                if (CommonHelper.countObject(data) === 0) {
                    sheet.commit();
                    workbook.commit();
                } else {
                    interface ds {
                        module: string,
                        description: string
                        action: string
                        url: any
                        created_at: Date
                        by: string
                    }

                    data.forEach((item: UserActivity) => {
                        const di: ds = {
                            module: item.module,
                            description: item.description,
                            action: ActivityTypeReverse[item.action],
                            created_at: item.created_at,
                            by: item.user.username,
                            url: item.url
                        }

                        sheet.addRow(di).commit()
                    });

                    if (CommonHelper.countObject(data) == limit) {
                        fetchAndWrite(page + 1);
                    } else {
                        sheet.commit();
                        workbook.commit();
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

    static async create(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Activity']
        #swagger.security = [{
            "bearerAuth": []
        }]
        
         #swagger.requestBody = {
            required: true,
            description: "Use this to fill action Create = C, View = V, Update = U, Delete = D, Restore = T",
            content: {
                "application/json": {
                    schema: {
                         $ref: "#/components/schemas/user_activity"
                    }  
                }
            }
        }
        */

        try {
            const schema = Joi.object().keys({
                id_user: Joi.string().uuid().required().label('ID User'),
                refresh_token: Joi.string().uuid().required().label('Refresh Token'),
                module: Joi.string().required().label('Module'),
                description: Joi.string().required().label('Description'),
                action: Joi.string().allow(...Object.values(ActivityType)).required().label('Action'),
                url: Joi.string().optional().allow('').label('URL'),
                data_body: Joi.string().optional().allow('').label('data_body'),
                application: Joi.string().required().label('Application'),
            });

            const param: UserActivity & { application: string, id_user: string } = await schema.validateAsync(req.body);

            const repo_app = OrmHelper.DB.getRepository(Application);
            const repo_user = OrmHelper.DB.getRepository(User);

            const data = new UserActivity()
            data.user = await repo_user.findOneBy({ id: param.id_user })
            data.refresh_token = param.refresh_token
            data.module = param.module
            data.description = param.description
            data.action = param.action
            data.url = param.url
            data.data_body = param.data_body
            data.application = await repo_app.findOneBy({ id: param.application });

            await OrmHelper.DB.manager.save(data);

            return ReturnHelper.successResponseAny(res, 200, Language.lang.success_insert, {});
        } catch (e: unknown) {
            log.error(e);
            const err = e as Error;

            return ReturnHelper.errorResponse(res, 500, 401, Language.lang.failed_insert, err.message);
        }
    }
}