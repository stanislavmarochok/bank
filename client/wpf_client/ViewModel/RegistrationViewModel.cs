using System.Collections.Generic;
using System.Net;
using System.Windows;
using System.Windows.Input;

namespace wpf_client.ViewModel
{
    class RegistrationViewModel : ViewModelBase
    {
        public string Username { get; set; }
        public string Email { get; set; }
        public string Lastname { get; set; }
        public string Firstname { get; set; }
        public string Password { get; set; }
        public string ConfirmPassword { get; set; }

        public ICommand RegisterClick { get; set; }
        public ICommand LoginClick { get; set; }

        public RegistrationViewModel(Window view_parent) : base(view_parent)
        {
            this.RegisterClick = new RelayCommand(o => Register());
            this.LoginClick    = new RelayCommand(o => { this.OpenWindow(new LoginView()); });
        }

        private void Register()
        {
            var response = request(RequestType.POST, "auth/signup",
                new Dictionary<string, string>
                {
                    { "username",              this.Username },
                    { "email",                 this.Email },
                    { "last_name",             this.Lastname },
                    { "first_name",            this.Firstname },
                    { "password",              this.Password },
                    { "password_confirmation", this.ConfirmPassword }
                });

            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyString);
            }
            else
            {
                Settings.Default.Email = this.Email;
                Settings.Default.Save();

                this.OpenWindow(new LoginView());
            }
        }
    }
}
