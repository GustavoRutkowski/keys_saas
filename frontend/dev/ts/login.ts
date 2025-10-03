import ToggleViewButton from './components/ToggleViewButton';
import { IUserCredentials } from './interfaces/IUser';
import User from './models/User';
import LocalData from './utils/LocalData';

ToggleViewButton.createAllButtons();

// login.js
const loginForm = document.querySelector('form#login-form') as HTMLFormElement;

loginForm.addEventListener('submit', async e => {
    e.preventDefault();

    const form = new FormData(loginForm);

    const EMAIL_REGEXP = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    if (!EMAIL_REGEXP.test(form.get('email') as string)) {
        alert('invalid email format');
    }

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
    alert(loginResponse.message);
});
