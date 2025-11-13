package com.example.sae32;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

public class MainActivity extends AppCompatActivity {

    Button plusButton;
    ListView listView;
    ArrayAdapter<String> adapter;
    ArrayList<String> vulnerabilitiesList = new ArrayList<>();
    private static final String API_URL = "http://10.0.2.2:8080/vulnerability.php";

    @SuppressLint("MissingInflatedId")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);

        listView = findViewById(R.id.vulnList);
        adapter = new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, vulnerabilitiesList);
        listView.setAdapter(adapter);

        plusButton = findViewById(R.id.plusButton);
        plusButton.setOnClickListener(v -> {
            Intent intent = new Intent(MainActivity.this, Desc_vulnerability.class);
            startActivity(intent);
        });

        new FetchDataTask().execute(API_URL);
    }

    private class FetchDataTask extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... urls) {
            StringBuilder result = new StringBuilder();
            try {
                URL url = new URL(urls[0]);
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("GET");
                conn.setConnectTimeout(8000);
                conn.setReadTimeout(8000);

                BufferedReader reader = new BufferedReader(
                        new InputStreamReader(conn.getInputStream()));
                String line;
                while ((line = reader.readLine()) != null) {
                    result.append(line);
                }
                reader.close();
                return result.toString();

            } catch (Exception e) {
                return "ERROR: " + e.getMessage();
            }
        }

        @Override
        protected void onPostExecute(String response) {
            if (response == null || response.startsWith("ERROR")) {
                Toast.makeText(MainActivity.this,
                        response == null ? "Unknown error" : response,
                        Toast.LENGTH_LONG).show();
                return;
            }

            try {
                vulnerabilitiesList.clear();

                if (response.trim().startsWith("[")) {
                    JSONArray arr = new JSONArray(response);
                    for (int i = 0; i < arr.length(); i++) {
                        JSONObject vuln = arr.getJSONObject(i);
                        String target = vuln.optString("target", "-");
                        String title = vuln.optString("title", "-");
                        String severity = vuln.optString("severity", "-");
                        vulnerabilitiesList.add(target + " - " + title + " - " + severity);
                    }
                }

                else {
                    JSONObject jsonObject = new JSONObject(response);
                    JSONArray dataArray = jsonObject.getJSONArray("data");
                    for (int i = 0; i < dataArray.length(); i++) {
                        JSONObject vuln = dataArray.getJSONObject(i);
                        String ip = vuln.optString("ip", "-");
                        String service = vuln.optString("service", "-");
                        String severity = vuln.optString("severity", "-");
                        vulnerabilitiesList.add(ip + " - " + service + " - " + severity);
                    }
                }

                adapter.notifyDataSetChanged();

            } catch (Exception e) {
                Toast.makeText(MainActivity.this,
                        "Parse error: " + e.getMessage(), Toast.LENGTH_LONG).show();
            }
        }
    }
}
