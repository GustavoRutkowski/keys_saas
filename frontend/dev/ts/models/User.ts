import Api from "../utils/Api";
import IUser from '../interfaces/IUser';

interface IUserCredentials {
    email: string;
    main_pass: string;
}

class User {
    private static api: Api = new Api({ url: 'http://localhost:2469/backend' });

    // Create
    public static async create(userInfos: IUser): Promise<any> {
        const createData = await this.api.post('users', userInfos);
        return createData;
    }

    // Login
    public static async login(credentials: IUserCredentials) {
        const userLogged = await this.api.post('users/login', credentials);
        return userLogged;
    }
}

export default User;
