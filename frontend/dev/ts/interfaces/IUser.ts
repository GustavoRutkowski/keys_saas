interface IUserCredentials {
    email: string;
    main_pass: string;
}
interface IUser extends IUserCredentials {
    name: string;
};

export default IUser;
export { IUserCredentials };