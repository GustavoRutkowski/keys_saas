import Api from "../utils/Api";
import IUser, { IUserCredentials, IUserUpdateInfos } from '../interfaces/IUser';
import IResponse from "../interfaces/IResponse";
import LocalData from "../utils/LocalData";


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

    // Get
    public static async get(): Promise<IResponse> {
        this.api.addHeader('token', LocalData.get('token') as string);

        const user: IResponse = await this.api.get('users/user');        
        return user;
    }

    // Update Infos:
    public static async updateInfos(infos: IUserUpdateInfos): Promise<IResponse> {
        console.log(`Token: ${LocalData.get('token')}`)
        this.api.addHeader('token', LocalData.get('token') as string);

        const response: IResponse = await this.api.put('users/user', infos);
        return response;
    }
}

export default User;
