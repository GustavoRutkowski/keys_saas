function toBase64(file: File): string | null {
    const fileReader: FileReader = new FileReader();

    let base64: string | null = null;

    fileReader.onload = e => base64 = e.target?.result as string;
    fileReader.readAsDataURL(file);
    
    return base64;
}

export default toBase64;
