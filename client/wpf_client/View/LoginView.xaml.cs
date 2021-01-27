using System.Windows;
using wpf_client.ViewModel;

namespace wpf_client
{
    public partial class LoginView : Window
    {
        public LoginView()
        {
            InitializeComponent();
            this.DataContext = new LoginViewModel(this);
        }
    }
}
