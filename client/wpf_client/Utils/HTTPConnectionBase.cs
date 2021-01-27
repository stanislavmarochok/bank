using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace wpf_client.Utils
{
    class HTTPConnectionBase
    {
        protected enum RequestType
        {
            POST, 
            GET, 
            PUT
        }

        protected static object request(RequestType requestType, string url, Dictionary<string, string> data = null)
        {
            HttpClient client = new HttpClient();
            client.BaseAddress = new Uri("http://localhost:8000/api/");

            HttpResponseMessage response = client.PostAsync(url, data != null ? new FormUrlEncodedContent(data) : null).Result;
            if (response.IsSuccessStatusCode)
            {
                JObject json = JObject.Parse(response.Content.ReadAsStringAsync().GetAwaiter().GetResult());
                return json;
            }
            else
            {
                return false;
            }
        }
    }
}
