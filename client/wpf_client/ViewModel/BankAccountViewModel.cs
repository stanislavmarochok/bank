using System;
using System.Collections.Generic;
using System.Net;
using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Input;

namespace wpf_client.ViewModel
{
    class BankAccountViewModel : ViewModelBase
    {
        public string FullName { get; set; }

        private decimal amount;
        public string Amount {
            get {
                return this.amount.ToString();
            }
            set {
                this.amount = decimal.Parse(value);
                RaisePropertyChanged("Amount");
            }
        }

        private decimal amountToAddCharge;
        public string AmountToAddCharge
        {
            get {
                return this.amountToAddCharge.ToString();
            }
            set {
                this.amountToAddCharge = decimal.Parse(Regex.Replace(value, "[^0-9.]", ""));
                RaisePropertyChanged("AmountToAddCharge");
            }
        }

        public ICommand LogoutClick { get; set; }
        public ICommand AddClick { get; set; }
        public ICommand ChargeClick { get; set; }

        public BankAccountViewModel(Window view_parent) : base(view_parent) {
            this.LogoutClick = new RelayCommand(o => Logout());
            this.AddClick    = new RelayCommand(o => Add());
            this.ChargeClick = new RelayCommand(o => Charge());

            if (!isAuthorized()) {
                this.OpenWindow(new LoginView());
                return;
            }

            this.getUser();
            this.getBankAccount();
        }

        private void getUser()
        {
            var response = request(RequestType.GET, "auth/user");
            if (response.StatusCode != HttpStatusCode.OK) {
                MessageBox.Show(response.BodyString);
                if (response.StatusCode == HttpStatusCode.Unauthorized)
                {
                    this.OpenWindow(new LoginView());
                }
            }
            else {
                this.FullName = string.Format("{0} {1}", 
                    response.BodyJson.GetValue("first_name").ToString(), 
                    response.BodyJson.GetValue("last_name").ToString());
            }
        }

        private void getBankAccount()
        {
            var response = request(RequestType.GET, "bank/details");
            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyString);
            }
            else
            {
                this.Amount = response.BodyJson.GetValue("amount").ToString();
            }
        }

        private void Logout()
        {
            var response = request(RequestType.POST, "auth/signout");
            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyJson.ToString());
            }
            else
            {
                Settings.Default.AccessToken = string.Empty;
                Settings.Default.TokenExpirationSeconds = 0;
                Settings.Default.TimeTokenRecieved = DateTime.Now;
                Settings.Default.Save();

                this.OpenWindow(new LoginView());
            }
        }

        private void Add()
        {
            var response = request(RequestType.POST, "bank/add_amount",
                new Dictionary<string, string>
                {
                    { "amount", this.AmountToAddCharge },
                });

            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyJson.ToString());
            }
            else
            {
                this.getBankAccount();
            }
        }

        private void Charge()
        {
            var response = request(RequestType.POST, "bank/charge_amount",
                new Dictionary<string, string>
                {
                    { "amount", this.AmountToAddCharge },
                });

            if (response.StatusCode != HttpStatusCode.OK)
            {
                MessageBox.Show(response.BodyJson != null ? response.BodyJson.ToString() : response.BodyString);
            }
            else
            {
                this.getBankAccount();
            }
        }
    }
}
