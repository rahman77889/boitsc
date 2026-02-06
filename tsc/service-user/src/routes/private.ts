import express from 'express';
import { Language } from '../langs/lang';
import { UserController } from '../controllers/user';
import { UserActivityController } from '../controllers/user_activity';
import { UserRefreshTokenController } from '../controllers/user_refresh_token';
import JwtHelper from '../helpers/jwt';
import { UserRoleController } from '../controllers/user_roles';

export class RoutePrivate {
    static setup(app: express.Application) {

        app.use(Language.apply)

        JwtHelper.secure(app);

        app.get('/api/user/list', UserController.list)
        app.get('/api/user/export', UserController.export)
        app.post('/api/user/create', UserController.create)
        app.put('/api/user/update/:id', UserController.update)
        app.put('/api/user/add_role/:id/:id_role', UserController.addRole)
        app.put('/api/user/delete_role/:id/:id_role', UserController.deleteRole)
        app.put('/api/user/update_profile', UserController.updateProfile)
        app.put('/api/user/update_password', UserController.updatePassword)
        app.get('/api/user/detail/:id', UserController.detail)
        app.delete('/api/user/delete/:id/:hard', UserController.delete)
        app.put('/api/user/restore/:id', UserController.restore)

        app.get('/api/user_role/list', UserRoleController.list)
        app.get('/api/user_role/export', UserRoleController.export)
        // app.get('/api/user_role/role', UserRoleController.role)
        app.post('/api/user_role/create', UserRoleController.create)
        app.put('/api/user_role/update/:id', UserRoleController.update)
        app.delete('/api/user_role/delete/:id/:hard', UserRoleController.delete)
        app.put('/api/user_role/restore/:id', UserRoleController.restore)

        app.get('/api/user_activity/list', UserActivityController.list)
        app.get('/api/user_activity/export', UserActivityController.export)
        app.post('/api/user_activity/create', UserActivityController.create)

        app.get('/api/refresh_token/list', UserRefreshTokenController.list)
        app.put('/api/refresh_token/force_logout/:refresh_token', UserRefreshTokenController.forceLogout)
    }
}