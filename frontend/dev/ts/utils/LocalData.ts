class LocalData {
    public static get(key: string): string | object {
        if (typeof key === 'string') return localStorage.getItem(key) as string;
        return JSON.parse(localStorage.getItem(key) as string);
    }

    public static set(key: string, value: string | object): void {
        localStorage.setItem(key, typeof value === 'string' ? value : JSON.stringify(value));
    }

    public static remove(key: string): void {
        localStorage.removeItem(key);
    }
}

export default LocalData;
