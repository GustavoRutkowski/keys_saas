import './components/toggle-view-btn';
import User from './models/User';
import IUser from './interfaces/IUser';


console.warn('Test 2...');
// register.js
const registerForm = document.querySelector('form#register-form') as HTMLFormElement;
const messageParagraph = registerForm.querySelector('p.form-message') as HTMLParagraphElement;

registerForm.addEventListener('submit', async e => {
    e.preventDefault();

    const form = new FormData(registerForm);

    const userInfos: IUser = {
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
