import Modal from "./Modal";

class ChangeMainPassModal extends Modal {
    protected customClass: string = 'change-password-modal';

    constructor() { super(); }

    private confirmNewPassword(): void {
        // Lógica de requisição para change main pass...
        console.log('In progress...');
    }

    protected createModalContent(): void {
        const modal = this.element as HTMLDialogElement;
        modal.innerHTML += `
            <h2>Trocar Senha</h2>

            <form class="change-password-modal__form">
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
                        <input name="confirm_new_main_pass" class="form-field__input" id="confirm-new-pass-input" type="password" placeholder="Digite sua senha" autocomplete="current-password">
                        <!-- BOTÃO SERÁ INSERIDO DINAMICAMENTE -->
                    </div>
                </fieldset>
            </form>
        `;

        const confirmNewPassBtn = document.createElement('button') as HTMLButtonElement;
        confirmNewPassBtn.id = 'confirm-new-password-btn';

        confirmNewPassBtn.innerHTML = `
            <i class="fa-solid fa-key"></i>
            <span>Trocar Senha</span>
        `;

        confirmNewPassBtn.addEventListener('click', this.confirmNewPassword);
        modal.appendChild(confirmNewPassBtn);

        this.element = modal;
    }
}

export default ChangeMainPassModal;
