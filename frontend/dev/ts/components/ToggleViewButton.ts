import UIComponent from "./UIComponent";

// const cssRaw = `
//     @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

//     :host {
//         min-width: fit-content;
//         min-height: fit-content;
//         color: rgba(255, 255, 255, .4);
//         cursor: pointer;
//         transition-duration: .3s;
//     }
//     :host i {
//         display: inline-block;
//         min-width: 20px;
//         min-height: 20px;
//         color: rgba(255, 255, 255, .4);
//     }
//     :host:hover { color: white }
// `;

class ToggleViewButton extends UIComponent {
    private static VISIBLE_ICON = 'fa-eye-slash';
    private static HIDDEN_ICON = 'fa-eye';

    protected identifierClass: string = 'toggle-view-btn';

    private target: HTMLInputElement;
    private title: string;
    private visible: boolean;

    constructor(target: HTMLInputElement, title: string = 'exibir') {
        super();

        this.target = target;
        this.title = title;
        this.visible = false;
        this.createElement();
    }

    public isVisible(): boolean { return this.visible }

    public appendBelowTarget(): void {
        this.target.insertAdjacentElement('afterend', this.element as Element);
    }

    public static createAllButtons(node: ParentNode = document): void {
        const inputPasswords = node.querySelectorAll('input[type="password"]') as NodeListOf<HTMLInputElement>;

        inputPasswords.forEach(input => {
            const toggleViewBtn = new ToggleViewButton(input);
            toggleViewBtn.appendBelowTarget();
        });
    }

    private renderIcon(): void {
        this.target.type = this.visible ? 'text' : 'password';

        const selectedIcon: string = this.visible
            ? ToggleViewButton.VISIBLE_ICON
            : ToggleViewButton.HIDDEN_ICON;

        const i: HTMLElement | null = this.element?.querySelector('i') || null;
        i?.setAttribute('class', `fa-solid ${selectedIcon}`);
    }

    private toggleVisibility(): void {
        this.visible = !this.visible;
        this.renderIcon();
    }

    protected createElement() {
        const button = document.createElement('button') as HTMLButtonElement;
        button.classList.add(this.identifierClass);
        button.title = this.title;
        button.setAttribute('tabindex', '0');

        const selectedIcon: string = this.visible
            ? ToggleViewButton.VISIBLE_ICON
            : ToggleViewButton.HIDDEN_ICON;

        button.innerHTML = `<i class="fa-solid ${selectedIcon}"></i>`;
        button.addEventListener('click', e => {
            e.preventDefault();
            this.toggleVisibility();
        });

        this.element = button;
    }
}

export default ToggleViewButton;
