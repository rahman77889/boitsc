import config from 'config';
import { DataSource } from "typeorm";
import { Application, Menu, User, UserActivity, UserRefreshToken, UserRole } from "entity";
import { ILogObj, Logger } from 'tslog';

export class OrmHelper {
    static DB: DataSource = null

    static setup() {
        const log: Logger<ILogObj> = new Logger({ name: '[OrmHelper]', type: 'pretty' });

        const engine: 'mysql' | 'postgres' = config.get("database.engine")

        OrmHelper.DB = new DataSource({
            type: engine,
            host: config.get("database.host"),
            port: Number(config.get("database.port")),
            username: String(config.get("database.username")),
            password: String(config.get("database.password")),
            database: String(config.get("database.database")),
            synchronize: true,
            logging: config.get('database.logging'),
            entities: [User, UserRole, UserActivity, UserRefreshToken, Menu, Application],
            subscribers: [],
            migrations: [],
        })

        OrmHelper.DB.initialize()
            .then(() => {
                // here you can start to work with your database
            })
            .catch((error: any) => log.error(error))
    }
}