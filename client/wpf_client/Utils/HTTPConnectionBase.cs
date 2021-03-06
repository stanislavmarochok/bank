﻿using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Windows;

namespace wpf_client.Utils
{

    /**
     * This class is used for simplified work with HTTP responses, it contains only necceary parameters
     */
    class HTTPResponse
    {
        public HttpStatusCode StatusCode = HttpStatusCode.OK;
        public JObject BodyJson = null;
        public string BodyString = null;
    }

    /**
     * This class implements methods for HTTP requests and authorization control
     */
    class HTTPConnectionBase
    {
        protected enum RequestType { POST, GET, PUT }

        protected static HTTPResponse request(RequestType requestType, string url, Dictionary<string, string> data = null)
        {
            HttpClient client = new HttpClient();
            client.BaseAddress = new Uri("http://localhost:8000/api/");
            client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", Settings.Default.AccessToken);
            
            HttpResponseMessage response = null;
            try
            {
                if (requestType == RequestType.POST)
                    response = client.PostAsync(url, data != null ? new FormUrlEncodedContent(data) : null).Result;
                else
                    response = client.GetAsync(url).Result;
            } catch (Exception e)
            {

            }

            var result = new HTTPResponse();

            if (response == null)
            {
                result.StatusCode = HttpStatusCode.BadRequest;
                result.BodyString = @"Server not responses";
                return result;
            }

            result.StatusCode = response.StatusCode;
            result.BodyString = response.Content.ReadAsStringAsync().GetAwaiter().GetResult();
            if (result.BodyString != null)
            {
                try
                {
                    result.BodyJson = JObject.Parse(result.BodyString);
                }
                catch
                {
                    // do nothing;
                }
            }
            
            return result;
        }

        protected bool isAuthorized()
        {
            if (string.IsNullOrEmpty(Settings.Default.AccessToken)) {
                return false;
            }
            else {
                if (Settings.Default.TokenExpirationSeconds == 0) {
                    return false;
                }

                if (DateTime.Now.Subtract(Settings.Default.TimeTokenRecieved).TotalSeconds > Settings.Default.TokenExpirationSeconds) {
                    return false;
                }
            }

            return true;
        }
    }
}
