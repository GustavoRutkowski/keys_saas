import IResponse from "../interfaces/IResponse";
import IUserData, { IUserCredentials } from "../interfaces/IUser";
import LocalData from "../utils/LocalData";
import User from "./User";

class UserSession {
    // Sempre retorna o usuário, a menos que seja mandado para o login antes.
    public static async authenticate(): Promise<IUserData | void> {
        if (!this.isLogged()) return this.logout();

        const user = await User.get();
        if (!user.success) return this.logout();

        return user.data as IUserData;
    }

    public static isLogged(): boolean {
        return !!LocalData.get('token');
    }

    public static async login(credentias: IUserCredentials): Promise<IResponse | void> {
        const loginResponse = await User.login(credentias);

        if (loginResponse.success) {
            LocalData.set('token', loginResponse.data.token as string);

            location.href = '/dashboard';
            return;
        }

        return loginResponse;
    }

    public static logout(): void {
        LocalData.remove('token');
        sessionStorage.clear();
        location.href = '/login';
    }
}

export default UserSession;
