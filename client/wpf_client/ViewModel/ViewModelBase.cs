using System.ComponentModel;
using System.Windows;
using wpf_client.Utils;

namespace wpf_client.ViewModel
{
    class ViewModelBase : HTTPConnectionBase, INotifyPropertyChanged
    {
        public event PropertyChangedEventHandler PropertyChanged;
        public Window ViewParent;

        protected ViewModelBase(Window view_parent)
        {
            this.ViewParent = view_parent;
        }

        protected void OpenWindow(Window new_window)
        {
            new_window.Show();
            this.ViewParent.Close();
        }

        protected void RaisePropertyChanged(string property)
        {
            if (PropertyChanged != null)
            {
                PropertyChanged(this, new PropertyChangedEventArgs(property));
            }
        }
    }
}
