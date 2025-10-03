abstract class UIComponent {
    protected element: HTMLElement | null = null;
    protected abstract identifierClass: string;

    public getElement(): HTMLElement | null { return this.element; }
    // Can be overwritten
    public getCopy(): Node | null { return this.element?.cloneNode(true) || null; }

    protected abstract createElement(): void;

    public deleteElement(): void {
        this.element?.remove();
        this.element = null;
    }
}

export default UIComponent;
