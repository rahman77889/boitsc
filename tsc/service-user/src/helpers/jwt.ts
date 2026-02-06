import { NextFunction, Response } from 'express';
import { Logger, ILogObj } from 'tslog';
import { Request, expressjwt } from "express-jwt";
import fs from 'fs';
import { Language } from '../langs/lang';
import { ReturnHelper } from './express/return';
import express from 'express';

const log: Logger<ILogObj> = new Logger({ name: '[JwtHelper]', type: 'pretty' });

export default class JwtHelper {
    static secure = (app: express.Application) => {

        var publicKey = fs.readFileSync("src/helpers/key/public.key");

        app.use(
            expressjwt({
                secret: publicKey, algorithms: ["RS256"],
                getToken: function fromHeaderOrQuerystring(req): any {
                    if (req.headers.authorization && req.headers.authorization.split(' ')[0] === 'Bearer') {
                        return req.headers.authorization.split(' ')[1];
                    } else if (req.query && req.query.token) {
                        return req.query.token;
                    }
                    return null;
                },
            }).unless({ path: ["/token"] }),
            function (req: Request, res: Response, next: NextFunction) {
                if (!req.auth?.data.id) {
                    return ReturnHelper.errorResponse(res, 403, 666, Language.lang.failed_access);
                }

                return next();
            }
        )

        app.use(function (err: any, req: Request, res: Response, next: NextFunction) {
            if (err.name === 'UnauthorizedError') {
                return ReturnHelper.errorResponse(res, 403, 666, Language.lang.failed_access);
            }

            return next();
        });

    }
}