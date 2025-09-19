import './components/toggle-view-btn';
import IFile from './interfaces/IFile';
import IResponse from './interfaces/IResponse';
import { IUserUpdateInfos } from './interfaces/IUser';
import User from './models/User';
import UserSession from './models/UserSession';

const { data: user } = await UserSession.authenticate() as IResponse;

// Ao enviar foto de perfil
// Criptografa-la em Base64
// Fazer a requisiçãO

// Editar Informações:

const updateInfos: IUserUpdateInfos = {
    name: user.name,
    picture: null,
}

const nicknameTxt = document.querySelector('label.user-nickname__nickname') as HTMLLabelElement;
nicknameTxt.textContent = user.name;

nicknameTxt.addEventListener('focusout', () => {
    nicknameTxt.contentEditable = 'false';
});

const editNicknameBtn = document.querySelector('button#edit-nickname-btn') as HTMLButtonElement;
editNicknameBtn.addEventListener('click', () => {
    nicknameTxt.contentEditable = 'true';
    nicknameTxt.focus();
});

nicknameTxt.addEventListener('input', () => updateInfos.name = nicknameTxt.textContent);

const pictureImg = document.querySelector('label.user-infos__picture > img') as HTMLImageElement;
if (user.picture) pictureImg.src = `../public/imgs/upload/${user.picture}`;

const pictureInput = document.querySelector('input#user-picture-input') as HTMLInputElement;

pictureInput.addEventListener('change', () => {
    const imgFile = pictureInput.files?.[0] as File | null;
    if (!imgFile) return;

    const fileReader: FileReader = new FileReader();

    fileReader.onload = e => {
        const base64: string = e.target?.result as string;
        pictureImg.src = base64;
        
        const picture: IFile = {
            filename: imgFile.name,
            data: base64.split(',')[1] // Remove o prefixo
        };
        
        updateInfos.picture = picture;
    };

    fileReader.readAsDataURL(imgFile);
});

const saveChangesBtn = document.querySelector('button#save-changes-btn') as HTMLButtonElement;
saveChangesBtn.addEventListener('click', async e => {
    e.preventDefault();
    console.log(await User.updateInfos(updateInfos));

    alert('Atualizado com sucesso');
});