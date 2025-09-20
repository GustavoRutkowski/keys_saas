interface IResponse<T = any> {
    status: number
    message: string
    success: boolean
    data?: T
}

export default IResponse;
