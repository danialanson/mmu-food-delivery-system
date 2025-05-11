profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
    profile.classList.toggle('active');
}

window.onscroll = () => {
    profile.classList.remove('active');
}

