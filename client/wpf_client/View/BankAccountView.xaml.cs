using System.Windows;
using wpf_client.ViewModel;

namespace wpf_client
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class BankAccountView : Window
    {
        public BankAccountView()
        {
            InitializeComponent();
            this.DataContext = new BankAccountViewModel(this);
        }
    }
}
