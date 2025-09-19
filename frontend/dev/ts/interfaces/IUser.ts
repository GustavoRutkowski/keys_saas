import IFile from "./IFile";

interface IUserCredentials {
    email: string;
    main_pass: string;
}
interface IUserUpdateInfos {
    name?: string,
    picture?: IFile | null
}

interface IUser extends IUserCredentials {
    name: string;
};

export default IUser;
export { IUserCredentials, IUserUpdateInfos };