import timeout from "../utils/timeout";
import UIComponent from "./UIComponent";

type TPopupType = 'success' | 'warn' | 'error';

class Popup extends UIComponent {
    private timeMs: number;
    private message: string;
    private type: TPopupType;

    protected identifierClass: string = 'pop-up';

    private static SUCCESS_ICON = 'fa-check';
    private static WARN_ICON = 'fa-triangle-exclamation';
    private static ERROR_ICON = 'fa-circle-xmark';

    constructor(message: string, timeMs: number = 3000, type: TPopupType = 'success') {
        super();
        this.timeMs = timeMs;
        this.message = message;
        this.type = type;

        this.createElement();
        document.body.appendChild(this.element as HTMLDialogElement);
    }

    public async show() {
        const popup = this.element as HTMLDialogElement;
        
        popup.show();
        await timeout(this.timeMs);
        popup.close();
    }
    
    protected createElement(): void {
        const popup = document.createElement('dialog') as HTMLDialogElement;
        popup.classList.add(this.identifierClass);
        popup.classList.add(this.type);

        const iconsMap: Record<TPopupType, string> = {
            'success': Popup.SUCCESS_ICON,
            'warn': Popup.WARN_ICON,
            'error': Popup.ERROR_ICON
        };

        const iconSelected: string = iconsMap[this.type];

        popup.innerHTML = `
            <i class="fa-solid ${iconSelected}"></i>
            <span>${this.message}</span>
        `;

        this.element = popup;
    }
}

export default Popup;
