using System.Windows;
using wpf_client.Utils;

namespace wpf_client.ViewModel
{
    class ViewModelBase : HTTPConnectionBase
    {
        public Window ViewParent;

        protected ViewModelBase(Window view_parent)
        {
            this.ViewParent = view_parent;
        }
    }
}
