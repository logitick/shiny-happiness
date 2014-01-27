using System;
using SimpleJSON;
using UnityEngine;
using System.Collections;
using System.Collections.Generic;

namespace RPS.Models
{
	public abstract class Model
	{
		protected static readonly string HTTP_FORM_KEY_INSERT = "insert";
		protected static readonly string HTTP_FORM_KEY_UPDATE = "update";

		protected List<string> apiPathFragments;

		protected bool isJSONConverted = false;
		protected HTTPDataProvider dataProvider;
		protected JSONNode jsonData;

		protected void FetchHTTPData() {
			this.dataProvider = new HTTPDataProvider (
				HTTPDataProvider.FindUrl(this)
			);
		}

		protected void PostHTTPData(string key, JSONNode value) {
			this.dataProvider = HTTPDataProvider.PostJSONData (HTTPDataProvider.FindUrl (this), key, value);
		}

		protected void AddApiPathFragment(string fragment) {
			if (this.apiPathFragments == null) {
				this.apiPathFragments = new List<string> ();
			}
			this.apiPathFragments.Add (fragment);
		}
		protected void AddApiPathFragment(object fragment) {

			this.AddApiPathFragment (fragment.ToString ());
		}


		protected void RemoveApiPathFragment(string fragment) {
			if (this.apiPathFragments != null) {
				this.apiPathFragments.Remove (fragment);
			}
		}
		
		/// <summary>
		/// Update the models data in the database
		/// </summary>
		public abstract bool Update();
		
		/// <summary>
		/// Inserts the data into the database
		/// </summary>
		public abstract bool Insert();

		protected string GetValue(string key) {
			if (this.jsonData == null) {
				return null;	
			}
			return this.jsonData ["result"][key].Value;
		}

		protected object GetValue(string key, TypeCode typecode) {

			try {
				switch (typecode) {
				case TypeCode.String:
					return this.jsonData ["result"][key].ToString();
				case TypeCode.Boolean:
					return this.jsonData ["result"][key].AsBool;
				case TypeCode.Int32:
					return this.jsonData ["result"][key].AsInt;
				case TypeCode.DateTime:
					return DateTime.Parse(this.jsonData ["result"][key].Value.ToString());
				}
			} catch (FormatException e) {
				Debug.LogError(this.jsonData);
			}

			return null;
		}


		public bool IsReady() {
			if (this.dataProvider != null && this.dataProvider.IsDone ()) {
				if (!this.isJSONConverted) {
					this.jsonData = dataProvider.GetJSONData();
					if (this.jsonData["metadata"]["status"].AsInt == 404) {
						throw new ModelNotFoundException(this);
					} else {
						ConvertToModel();
					}
					this.isJSONConverted = true;
				}
				return true;
			}
			return false;
		}

		public abstract void ConvertToModel ();
		public abstract string getApiPath();

	}
}