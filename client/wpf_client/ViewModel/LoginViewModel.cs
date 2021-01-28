using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.Net;
using System.Windows;
using System.Windows.Input;

namespace wpf_client.ViewModel
{
    class LoginViewModel : ViewModelBase
    {
        public string Email { get; set; }
        public string Password { get; set; }
        public ICommand LoginClick { get; set; }
        public ICommand RegisterClick { get; set; }

        public LoginViewModel(Window view_parent) : base(view_parent)
        {
            this.Email = Settings.Default.Email;
            this.LoginClick = new RelayCommand(o => Login());
            this.RegisterClick = new RelayCommand(o => {
                this.OpenWindow(new RegistrationView());
            });
        }

        private void Login()
        {
            this.ViewParent.Focus();

            var response = request(RequestType.POST, "auth/signin",
                new Dictionary<string, string>
                {
                    { "email",    this.Email },
                    { "password", this.Password }
                });

            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyString);
            }
            else
            {
                Settings.Default.AccessToken = response.BodyJson.GetValue("access_token").ToString();
                Settings.Default.TimeTokenRecieved = DateTime.Now;
                Settings.Default.TokenExpirationSeconds = int.Parse(response.BodyJson.GetValue("expires_in").ToString());
                Settings.Default.Email = this.Email;
                Settings.Default.Save();

                this.OpenWindow(new BankAccountView());
            }
        }
    }
}
