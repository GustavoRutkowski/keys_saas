interface ICancelablePromise<T> extends Promise<T> {
    cancel?: () => void;
} 

function timeout(ms: number): Promise<any> {
    let cancel: () => void;

    const promise: ICancelablePromise<any> = new Promise((resolve, reject) => {
        const timeout = setTimeout(resolve, ms);

        cancel = function() {
            clearTimeout(timeout);
            reject(new Error('Cancelled'));
        };
    }) as ICancelablePromise<any>;
    
    promise.cancel = cancel!;
    return promise;
}

export default timeout;
