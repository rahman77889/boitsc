import { Logger, ILogObj } from 'tslog';
import { validate } from 'uuid';

const log: Logger<ILogObj> = new Logger({ name: '[CommonHelper]', type: 'pretty' });

export default class CommonHelper {
    static randomInteger(min: number, max: number): number | null {
        if (min > 0 && max > 0) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        } else {
            log.error(new Error('Min & Max must be > 0'));
            return null
        }
    }

    static inArray(array: string[], keyword: string): boolean {
        for (let val of array) {
            if (val === keyword) {
                return true;
            }
        }

        return false;
    }

    static async sleep(seconds: number): Promise<void> {
        if (seconds > 0) {
            return new Promise((resolve) => setTimeout(resolve, seconds * 1000));
        } else {
            log.error(new Error('Second must be > 0'));
        }
    }

    static convertArrayToObject(object: any, key: string, val = ''): any {
        const ret: any = {};

        for (let i in object) {
            const d = object[i];

            if (val) {
                ret[d[key]] = d[val];
            } else {
                ret[d[key]] = d;
            }
        }

        return ret;
    }

    static objectFlip(obj: any): any {
        const ret = {};

        Object.keys(obj).forEach(key => {
            ret[obj[key]] = key;
        });

        return ret;
    }

    static countObject(obj: any): number {
        let c = 0;

        for (let s in obj) {
            c++;
        }

        return c;
    }

    static capitalizeFirstLetter(str: string): string {
        if (str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        return '';
    }

    static getKeyEnum(obj: any, key: any): any {
        try {
            return Object.values(obj)[Object.keys(obj)[key]]
        } catch (e: any) {
            return '';
        }
    }

    static generateTemplate(template: string, pairMap: any): string {
        for (let p in pairMap) {
            let v = pairMap[p];

            template = template.split('{' + p + '}').join(v);
        }

        return template
    }

    static handleFilter(param: { filter: any, col_any_eq: string[], col_any_like: string[], additional_where?: string }): any {
        const whereAttrPre = [];
        const whereVal: any = {};

        if (param.filter) {
            try {
                param.filter = JSON.parse(param.filter);

                let filter_any = false;
                let filter_any_val = '';

                for (let p in param.filter) {
                    let f = String(param.filter[p])
                    let fr = param.filter[p]

                    if (p != 'any') {
                        if (!validate(f)) {
                            let found = false;
                            if (fr['from']) {
                                whereAttrPre.push(p + " >= :" + p + '_from');
                                whereVal[p + '_from'] = fr['from'];
                                found = true;
                            }
                            if (fr['to']) {
                                whereAttrPre.push(p + " <= :" + p + '_to');
                                whereVal[p + '_to'] = fr['to'];
                                found = true;
                            }
                            if (fr['like']) {
                                whereAttrPre.push('LOWER(' + p + ') like :' + p + '_like');
                                whereVal[p + '_like'] = '%' + fr['like'].toLowerCase() + '%';;
                                found = true;
                            }

                            if (!found) {
                                whereAttrPre.push("LOWER(" + p + ") = :" + p);
                                f = f.toLowerCase();

                                whereVal[p] = f;
                            }
                        } else {//uuid only
                            whereAttrPre.push(p + " = :" + p);
                            whereVal[p] = f;
                        }
                    } else {
                        filter_any = true;
                        filter_any_val = f;
                    }
                }

                if (filter_any) {
                    let wtp = []
                    if (param.col_any_eq.length > 0) {
                        const eqr = [];
                        for (let c of param.col_any_eq) {
                            eqr.push('LOWER(' + c + ') = :filter_eq');
                        }
                        const eq = '(' + eqr.join(' or ') + ')';

                        wtp.push(eq);
                    }
                    if (param.col_any_like.length > 0) {
                        const liker = [];
                        for (let c of param.col_any_like) {
                            liker.push('LOWER(' + c + ') like :filter_like');
                        }

                        const like = '(' + liker.join(' or ') + ')';

                        wtp.push(like);
                    }

                    if (wtp.length > 0) {
                        whereAttrPre.push('(' + wtp.join(' or ') + ')');
                    }

                    if (param.col_any_eq.length > 0) {
                        whereVal['filter_eq'] = filter_any_val;
                    }
                    if (param.col_any_like.length > 0) {
                        whereVal['filter_like'] = '%' + filter_any_val + '%';
                    }
                }
            } catch (e: any) {
                let wtp = []
                if (param.col_any_eq.length > 0) {
                    const eqr = [];
                    for (let c of param.col_any_eq) {
                        eqr.push('LOWER(' + c + ') = :filter_eq');
                    }
                    const eq = '(' + eqr.join(' or ') + ')';

                    wtp.push(eq);
                }
                if (param.col_any_like.length > 0) {
                    const liker = [];
                    for (let c of param.col_any_like) {
                        liker.push('LOWER(' + c + ') like :filter_like');
                    }

                    const like = '(' + liker.join(' or ') + ')';

                    wtp.push(like);
                }

                if (wtp.length > 0) {
                    whereAttrPre.push('(' + wtp.join(' or ') + ')');
                }


                if (param.col_any_eq.length > 0) {
                    whereVal['filter_eq'] = param.filter.toLowerCase();
                }
                if (param.col_any_like.length > 0) {
                    whereVal['filter_like'] = '%' + param.filter + '%';
                }

            }
        }

        const whereAttr = whereAttrPre.join(' and ') + (whereAttrPre.length > 0 && param.additional_where && param.additional_where != '' ? ' and ' : '') + (param.additional_where ?? '');

        return { whereAttr, whereVal };
    }
}