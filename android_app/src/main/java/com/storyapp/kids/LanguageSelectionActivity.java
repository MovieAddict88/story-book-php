package com.storyapp.kids;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.storyapp.kids.models.Language;
import com.storyapp.kids.network.ApiClient;
import com.storyapp.kids.network.ApiService;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LanguageSelectionActivity extends AppCompatActivity {

    private RecyclerView recyclerView;
    private LanguageAdapter adapter;
    private ProgressBar progressBar;
    public static final String PREFS_NAME = "StoryAppPrefs";
    public static final String KEY_LANGUAGE_ID = "languageId";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Check if a language is already selected
        SharedPreferences prefs = getSharedPreferences(PREFS_NAME, MODE_PRIVATE);
        if (prefs.contains(KEY_LANGUAGE_ID)) {
            launchMainActivity();
            return; // Skip the rest of onCreate
        }

        setContentView(R.layout.activity_language_selection);

        recyclerView = findViewById(R.id.recyclerView_languages);
        progressBar = findViewById(R.id.progressBar);

        // Use a grid layout for large, friendly buttons
        recyclerView.setLayoutManager(new GridLayoutManager(this, 2));

        fetchLanguages();
    }

    private void fetchLanguages() {
        progressBar.setVisibility(View.VISIBLE);
        ApiService apiService = ApiClient.getApiService();
        Call<List<Language>> call = apiService.getLanguages();

        call.enqueue(new Callback<List<Language>>() {
            @Override
            public void onResponse(Call<List<Language>> call, Response<List<Language>> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    setupRecyclerView(response.body());
                } else {
                    Toast.makeText(LanguageSelectionActivity.this, "Failed to load languages.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Language>> call, Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(LanguageSelectionActivity.this, "An error occurred: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void setupRecyclerView(List<Language> languages) {
        adapter = new LanguageAdapter(languages, language -> {
            // Save the selected language ID
            SharedPreferences.Editor editor = getSharedPreferences(PREFS_NAME, MODE_PRIVATE).edit();
            editor.putInt(KEY_LANGUAGE_ID, language.getId());
            editor.apply();

            // Proceed to the main activity
            launchMainActivity();
        });
        recyclerView.setAdapter(adapter);
    }

    private void launchMainActivity() {
        Intent intent = new Intent(LanguageSelectionActivity.this, MainActivity.class);
        startActivity(intent);
        finish(); // Finish this activity so the user can't navigate back to it
    }
}