const swaggerAutogen = require('swagger-autogen')({ openapi: '3.0.0' });
const config = require('config');

const doc = {
    info: {
        title: config.get('app.name'),
        description: config.get('app.description'),
        version: config.get('app.version')
    },
    servers: [
        {
            url: config.get('server.host_swagger'),
            description: 'Environtment ' + config.get('app.env')
        },
    ],
    components: {
        schemas: {
            login: {
                $username: "admin",
                $password: "12345678",
                $application: "ukln",
            },
            user: {
                $nik: "123456789",
                $email: "admin@gmail.com",
                $username: "admin",
                $password: "12345aA!",
                $retype_password: "12345aA!",
                $name: "Administrator",
                $nik: "123456789",
                // $phone: "08123456789",
                // $jabatan: "4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8",
                // $organisasi: "4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8",
                // $divisi: "4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8",
                // $unit: "4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8",
                $status: "Y"
            },
            user_profile: {
                $email: "admin@gmail.com",
                $username: "admin",
                $name: "Administrator"
            },
            user_password: {
                $password: "123456",
                $retype_password: "123456"
            },
            user_role: {
                $name: "Administrator",
                $roles: ["4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8", "1a1221b6-78a0-4b3b-bf5c-fa0af5f69be8"],
                $status: "Y",
                $application: "ukln"
            },
            user_activity: {
                $id_user: "4a6991b6-78a0-4b3b-bf5c-fa0af5f69be8",
                $refresh_token: "1a1221b6-78a0-4b3b-bf5c-fa0af5f69be8",
                $module: "dashboard",
                $description: "Main Dashboard",
                $action: "V",
                $url: "https://bri-dev.shiblysolution.id/",
                $data_body: "{\"from\":\"2024-11-01\",\"to\":\"2024-11-26\"}",
                $application: "ukln"
            }
        },
        parameters: {

        },
        securitySchemes: {
            bearerAuth: {
                type: 'http',
                scheme: 'bearer'
            }
        }
    }
};

const outputFile = './swagger.json';
const routes = ['../routes/private.ts', '../routes/public.ts'];

swaggerAutogen(outputFile, routes, doc);

