import './components/toggle-view-btn';
import { IUserCredentials } from './interfaces/IUser';
import User from './models/User';
import LocalData from './utils/LocalData';


// login.js
const loginForm = document.querySelector('form#login-form') as HTMLFormElement;
const messageParagraph = loginForm.querySelector('p.form-message') as HTMLParagraphElement;

loginForm.addEventListener('submit', async e => {
    e.preventDefault();

    const form = new FormData(loginForm);

    const userCredentials: IUserCredentials = {
        email: form.get('email') as string,
        main_pass: form.get('main_pass') as string
    };

    const loginResponse = await User.login(userCredentials);

    if (loginResponse.success) {
        LocalData.set('token', loginResponse.data.token as string);

        location.href = '/dashboard';
        return;
    }
    
    for (const key of form.keys()) form.delete(key);
    messageParagraph.textContent = loginResponse.message;
});
