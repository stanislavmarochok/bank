using System.Windows;
using System.Windows.Controls;
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

        private void AmountToAddCharge_PreviewTextInput(object sender, System.Windows.Input.TextCompositionEventArgs e)
        {
            bool approvedDecimalPoint = false;

            if (e.Text == ".")
            {
                if (!((TextBox)sender).Text.Contains("."))
                    approvedDecimalPoint = true;
            }

            if (!(char.IsDigit(e.Text, e.Text.Length - 1) || approvedDecimalPoint))
                e.Handled = true;
        }
    }
}
