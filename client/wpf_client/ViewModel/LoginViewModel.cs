using Newtonsoft.Json.Linq;
using System.Collections.Generic;
using System.Windows;
using System.Windows.Input;

namespace wpf_client.ViewModel
{
    class LoginViewModel : ViewModelBase
    {
        public string Email { get; set; }
        public string Password { get; set; }
        public ICommand LoginClick { get; set; }

        public LoginViewModel(Window view_parent) : base(view_parent)
        {
            this.Email = Settings.Default.Email;
            this.LoginClick = new RelayCommand(o => Login());
        }

        private void Login()
        {
            var result = request(RequestType.POST, "http://localhost:8000/api/auth/signin",
                new Dictionary<string, string>
                {
                    { "email",    this.Email },
                    { "password", this.Password }
                });

            if (result is bool && !(bool)result)
            {
                MessageBox.Show(result.ToString());
            }
            else
            {
                Settings.Default.AccessToken = ((JObject)result).GetValue("access_token").ToString();
                Settings.Default.Save();
            }
        }
    }
}
