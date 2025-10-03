// Um modal é uma janela que abre a partir de um determinado disparador e que pode ser fechado
// Cada modal tem um conteúdo diferente

import UIComponent from "./UIComponent";

abstract class Modal extends UIComponent {
    protected identifierClass: string = 'modal';
    protected abstract customClass: string;

    constructor(open: boolean = false) {
        super();
        this.createElement();
        this.appendInBody();

        if (open) this.show();
    }
    
    public show(): void {
        const modal = this.element as HTMLDialogElement;
        modal.showModal();
    }

    public close(): void {
        const modal = this.element as HTMLDialogElement;
        modal.close();
    }

    public appendInBody(): void {
        document.body.appendChild(this.element as HTMLDialogElement);
    }

    protected abstract createModalContent(): void;

    protected createElement(): void {
        const modal = document.createElement('dialog') as HTMLDialogElement;
        modal.classList.add(this.identifierClass);
        
        modal.innerHTML = `
            <button class="modal__close-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        
        this.element = modal;
        this.createModalContent();

        const modalCloseBtn = modal.querySelector('button.modal__close-btn') as HTMLButtonElement;
        modalCloseBtn.addEventListener('click', () => this.close());
    }
}

export default Modal;
