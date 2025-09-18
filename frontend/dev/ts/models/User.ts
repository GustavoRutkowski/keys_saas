import Api from "../utils/Api";
import IUser, { IUserCredentials } from '../interfaces/IUser';
import IResponse from "../interfaces/IResponse";

class User {
    private static api: Api = new Api({ url: 'http://localhost:2469/backend' });

    // Create
    public static async create(userInfos: IUser): Promise<IResponse> {
        const createData: IResponse = await this.api.post('users', userInfos);
        return createData;
    }

    // Login
    public static async login(credentials: IUserCredentials): Promise<IResponse> {
        const userLogged: IResponse = await this.api.post('users/login', credentials);
        return userLogged;
    }
}

export default User;
