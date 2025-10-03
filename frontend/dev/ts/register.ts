import ToggleViewButton from './components/ToggleViewButton';
import User from './models/User';
import { IUserToCreate } from './interfaces/IUser';

ToggleViewButton.createAllButtons();

// register.js
const registerForm = document.querySelector('form#register-form') as HTMLFormElement;
const messageParagraph = registerForm.querySelector('p.form-message') as HTMLParagraphElement;

registerForm.addEventListener('submit', async e => {
    e.preventDefault();

    const form = new FormData(registerForm);

    const EMAIL_REGEXP = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    if (!EMAIL_REGEXP.test(form.get('email') as string)) {
        alert('invalid email format');
        form.delete('email');
        return;
    }

    if (form.get('main_pass')?.toString()?.length as number < 10) {
        alert('passoword must contain at least 10 characters');
        form.delete('main_pass');
        return;
    }

    const LETTERS_REGEXP = /[a-zA-Z]/;
    const NUMBERS_REGEXP = /[0-9]/;
    const SPECIAL_CHARS_REGEXP = /[^a-zA-Z0-9]+/g;

    if (!LETTERS_REGEXP.test(form.get('main_pass') as string)) {
        alert('passoword must contain letters');
        form.delete('main_pass');
        return;
    }

    if (!NUMBERS_REGEXP.test(form.get('main_pass') as string)) {
        alert('passoword must contain numbers');
        form.delete('main_pass');
        return;
    }

    if (!SPECIAL_CHARS_REGEXP.test(form.get('main_pass') as string)) {
        alert('passoword must contain at least one special character');
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
    messageParagraph.textContent = userCreated.message;
});
