import ToggleViewButton from './components/ToggleViewButton';
import User from './models/User';
import { IUserToCreate } from './interfaces/IUser';
import Popup from './components/Popup';

ToggleViewButton.createAllButtons();

// register.js
const registerForm = document.querySelector('form#register-form') as HTMLFormElement;

registerForm.addEventListener('submit', async e => {
    e.preventDefault();

    console.log('Enviado...')

    const form = new FormData(registerForm);

    const EMAIL_REGEXP = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    if (!EMAIL_REGEXP.test(form.get('email') as string)) {
        const popup = new Popup('invalid email format', 3000, 'error');
        await popup.show();
        form.delete('email');
        return;
    }

    if (form.get('main_pass')?.toString()?.length as number < 10) {
        const popup = new Popup('passoword must contain at least 10 characters', 3000, 'error');
        await popup.show();
        form.delete('main_pass');
        return;
    }

    const LETTERS_REGEXP = /[a-zA-Z]/;
    const NUMBERS_REGEXP = /[0-9]/;
    const SPECIAL_CHARS_REGEXP = /[^a-zA-Z0-9]+/g;

    if (!LETTERS_REGEXP.test(form.get('main_pass') as string)) {
        const popup = new Popup('passoword must contain letters', 3000, 'error');
        await popup.show();
        form.delete('main_pass');
        return;
    }

    if (!NUMBERS_REGEXP.test(form.get('main_pass') as string)) {
        const popup = new Popup('passoword must contain numbers', 3000, 'error');
        await popup.show();
        form.delete('main_pass');
        return;
    }

    if (!SPECIAL_CHARS_REGEXP.test(form.get('main_pass') as string)) {
        const popup = new Popup('passoword must contain at least one special character', 3000, 'error');
        await popup.show();
        form.delete('main_pass');
        return;
    }

    const userInfos: IUserToCreate = {
        name: form.get('name') as string,
        email: form.get('email') as string,
        main_pass: form.get('main_pass') as string
    };

    const userCreated = await User.create(userInfos);

    if (userCreated.success) {
        location.href = '/login';
        return;
    }
    
    for (const key of form.keys()) form.delete(key);
    const popup = new Popup(userCreated.message, 3000, 'error');
    await popup.show();
});
