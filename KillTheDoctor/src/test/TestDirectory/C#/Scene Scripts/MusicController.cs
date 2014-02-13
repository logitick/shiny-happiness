using UnityEngine;
using System.Collections;

public class MusicController : MonoBehaviour {

	void Awake()
	{

		DontDestroyOnLoad(this.gameObject);

	}
}
