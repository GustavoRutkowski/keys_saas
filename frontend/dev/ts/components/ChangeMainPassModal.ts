import { IUserChangePasswordInfos } from "../interfaces/IUser";
import User from "../models/User";
import Modal from "./Modal";
import Popup from "./Popup";

class ChangeMainPassModal extends Modal {
    protected customClass: string = 'change-password-modal';

    constructor() {
        super();
        this.element?.classList.add(this.customClass);
    }

    private addSubmitEvent(): void {
        const modal = this.element as HTMLDialogElement;
        const form = modal.querySelector('form') as HTMLFormElement;

        
        form.addEventListener('submit', async e => {
            e.preventDefault();
            console.log('click');
            
            const formData = new FormData(form);
            
            const credentials: IUserChangePasswordInfos = {
                main_pass: formData.get('main_pass') as string,
                new_main_pass: formData.get('new_main_pass') as string,
                repeat_new_main_pass: formData.get('repeat_new_main_pass') as string
            };

            const res = await User.changePassword(credentials);

            this.close();
            for (const key of form.keys()) form.delete(key);

            const popup = new Popup(res.message, 3000, res.success ? 'success' : 'error');
            await popup.show();
        });
    }

    protected createModalContent(): void {
        const modal = this.element as HTMLDialogElement;

        modal.innerHTML += `
            <h2>Trocar Senha</h2>

            <form id="change-password-modal__form" class="change-password-modal__form">
                <fieldset class="form__form-field">
                    <label for="main-pass-input">Senha Atual</label>

                    <div class="form-field__pass-container">
                        <input name="main_pass" class="form-field__input" id="main-pass-input" type="password" placeholder="Digite sua senha" autocomplete="current-password">
                        <!-- BOTÃO SERÁ INSERIDO DINAMICAMENTE -->
                    </div>
                </fieldset>

                <fieldset class="form__form-field">
                    <label for="new-pass-input">Nova Senha</label>

                    <div class="form-field__pass-container">
                        <input name="new_main_pass" class="form-field__input" id="new-pass-input" type="password" placeholder="Digite sua senha" autocomplete="current-password">
                        <!-- BOTÃO SERÁ INSERIDO DINAMICAMENTE -->
                    </div>
                </fieldset>

                <fieldset class="form__form-field">
                    <label for="confirm-new-pass-input">Confirmar a Nova Senha</label>

                    <div class="form-field__pass-container">
                        <input name="repeat_new_main_pass" class="form-field__input" id="confirm-new-pass-input" type="password" placeholder="Digite sua senha" autocomplete="current-password">
                        <!-- BOTÃO SERÁ INSERIDO DINAMICAMENTE -->
                    </div>
                </fieldset>
            </form>
        `;

        const confirmNewPassBtn = document.createElement('button') as HTMLButtonElement;
        confirmNewPassBtn.id = 'confirm-new-password-btn';
        confirmNewPassBtn.type = 'submit';
        confirmNewPassBtn.setAttribute('form', 'change-password-modal__form');

        confirmNewPassBtn.innerHTML = `
            <i class="fa-solid fa-key"></i>
            <span>Trocar Senha</span>
        `;

        modal.appendChild(confirmNewPassBtn);
        this.element = modal;
        this.addSubmitEvent();
    }
}

export default ChangeMainPassModal;
