function toBase64(file: File): Promise<string | null> {
    return new Promise((resolve, reject) => {
        const fileReader: FileReader = new FileReader();
        fileReader.readAsDataURL(file);
    
        let base64: string | null = null;
    
        fileReader.onload = e => {
            base64 = e.target?.result as string
            resolve(base64);
        };
        
        fileReader.onerror = e => reject(e);
    });
}

export default toBase64;
