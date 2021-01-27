using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Windows;

namespace wpf_client.ViewModel
{
    class BankAccountViewModel : ViewModelBase
    {
        public string FullName { get; set; }

        string accessToken;

        public BankAccountViewModel(Window view_parent) : base(view_parent)
        {
            accessToken = Settings.Default.AccessToken;
            if (string.IsNullOrEmpty(accessToken))
            {
                var loginWindow = new LoginView();
                loginWindow.Show();
                view_parent.Close();
                return;
            }

            this.getUser();
            this.getBankAccount();
        }

        private void getUser()
        {

        }

        private void getBankAccount()
        {

        }

        private async void requestAsync()
        {
        }
    }
}
