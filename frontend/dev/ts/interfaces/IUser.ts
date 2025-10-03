import IFile from "./IFile";

interface IUserCredentials {
    email: string;
    main_pass: string;
}
interface IUserUpdateInfos {
    name?: string;
    picture?: IFile | null;
}

interface IUserChangePasswordInfos {
    main_pass: string;
    new_main_pass: string;
    repeat_new_main_pass: string;
}

interface IUserToCreate extends IUserCredentials {
    name: string;
}

interface IUserData {
    id: number;
    name: string;
    email: string;
    picture: string;
}

export default IUserData;
export { IUserToCreate, IUserCredentials, IUserUpdateInfos, IUserChangePasswordInfos };
