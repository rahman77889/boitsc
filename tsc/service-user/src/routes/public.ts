import express from 'express';

import { AuthController } from '../controllers/auth';
import { Language } from '../langs/lang';

export class RoutePublic {
    static setup(app: express.Application) {

        app.use(Language.apply);

        app.post('/api/login', AuthController.login)
        app.put('/api/reset_password/:email/:application', AuthController.resetPassword)
        app.put('/api/update_password/:token', AuthController.updatePassword)
        app.put('/api/logout/:refresh_token', AuthController.logout)
        app.put('/api/renew_token/:refresh_token/:application', AuthController.renewToken)

    }
}