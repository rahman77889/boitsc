import { Response, NextFunction, Application } from "express";
import { Request } from "express-jwt";
import { ReturnHelper } from "../helpers/express/return";
import { OrmHelper } from "../helpers/orm";
import Joi from "joi";
import { ILogObj, Logger } from "tslog";
import { Language } from "../langs/lang";
import { Paging } from "entity";
import CommonHelper from "../helpers/common";
import { UserRefreshToken } from "entity";

const log: Logger<ILogObj> = new Logger({ name: '[UserRefreshTokenController]', type: 'pretty' });

export class UserRefreshTokenController {
    static async list(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Refresh Token']
        #swagger.parameters['filter'] = {
            description: 'Filter with 2 format : <ul><li>Simple format use text plaint will filter like %name%</li><li>Advance format using field existing {name:\'admin\', any:\'SAME AS PLAINT LOGIC\', etc...}</li></ul>',
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
                order_field: Joi.string().required().label('Order Field'),
                order_direction: Joi.string().allow('asc', 'desc').required().label('Order Direction'),
                token: Joi.string().required().label('Token'),
            });

            const param: Paging = await schema.validateAsync(req.query);

            const repo = OrmHelper.DB.getRepository(UserRefreshToken);

            const offset = (param.page - 1) * param.limit

            const { whereAttr, whereVal } = CommonHelper.handleFilter({
                filter: param.filter,
                col_any_eq: [],
                col_any_like: ['device', 'platform'],
                additional_where: 'id_user = :id_user'
            });

            whereVal['id_user'] = req.auth.data.id;

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

    static async forceLogout(req: Request, res: Response, next: NextFunction): Promise<Response> {
        /*
        #swagger.tags = ['User Refresh Token']
        #swagger.security = [{
            "bearerAuth": []
        }]
        #swagger.parameters['refresh_token'] = {
            in: 'path',
            description: 'Refresh Token.',
            required: true,
            type: 'string'
        }
        */
        try {
            const schema = Joi.object().keys({
                refresh_token: Joi.string().uuid().required().label('Refresh Token')
            });

            const param: UserRefreshToken = await schema.validateAsync(req.params);

            const repo = OrmHelper.DB.getRepository(UserRefreshToken);

            const affected = (await repo.delete({ refresh_token: param.refresh_token })).affected;

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
}