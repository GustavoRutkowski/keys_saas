// Um modal é uma janela que abre a partir de um determinado disparador e que pode ser fechado
// Cada modal tem um conteúdo diferente

import UIComponent from "./UIComponent";

abstract class Modal extends UIComponent {
    protected identifierClass: string = 'modal';
    protected abstract customClass: string;

    constructor(open: boolean = false) {
        super();
        this.createElement();

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

    protected abstract createModalContent(): void;

    protected createElement(): void {
        const modal = document.createElement('dialog') as HTMLDialogElement;
        modal.classList.add(this.identifierClass);

        const modalCloseBtn = document.createElement('button') as HTMLButtonElement;
        modalCloseBtn.classList.add('modal__close-btn');

        modalCloseBtn.innerHTML = `<i class="fa-solid fa-xmark"></i>`;
        modalCloseBtn.addEventListener('click', this.close);

        this.element = modal;
        this.createModalContent();
    }
}

export default Modal;
