package com.storyapp.kids;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.gson.Gson;
import com.storyapp.kids.db.AppDatabase;
import com.storyapp.kids.db.OfflineStory;
import com.storyapp.kids.models.Story;
import com.storyapp.kids.network.ApiClient;
import com.storyapp.kids.network.ApiService;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends AppCompatActivity {

    private RecyclerView recyclerViewStories;
    private StoryAdapter storyAdapter;
    private ProgressBar progressBar;
    private int languageId;
    private AppDatabase db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setTitle("Choose a Story");

        SharedPreferences prefs = getSharedPreferences(LanguageSelectionActivity.PREFS_NAME, MODE_PRIVATE);
        languageId = prefs.getInt(LanguageSelectionActivity.KEY_LANGUAGE_ID, -1);

        if (languageId == -1) {
            Toast.makeText(this, "Language not selected. Please restart the app.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        db = AppDatabase.getDatabase(getApplicationContext());
        progressBar = findViewById(R.id.progressBar_stories);
        recyclerViewStories = findViewById(R.id.recyclerView_stories);
        recyclerViewStories.setLayoutManager(new GridLayoutManager(this, 2));

        if (isNetworkAvailable()) {
            fetchStories();
        } else {
            Toast.makeText(this, "You are offline. Showing downloaded stories.", Toast.LENGTH_LONG).show();
            fetchStoriesFromDb();
        }
    }

    private boolean isNetworkAvailable() {
        ConnectivityManager connectivityManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
        return activeNetworkInfo != null && activeNetworkInfo.isConnected();
    }

    private void fetchStories() {
        progressBar.setVisibility(View.VISIBLE);
        ApiService apiService = ApiClient.getApiService();
        Call<List<Story>> call = apiService.getStories(languageId, null);

        call.enqueue(new Callback<List<Story>>() {
            @Override
            public void onResponse(Call<List<Story>> call, Response<List<Story>> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    setupRecyclerView(response.body());
                } else {
                    Toast.makeText(MainActivity.this, "Failed to load stories.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Story>> call, Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(MainActivity.this, "An error occurred: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void fetchStoriesFromDb() {
        progressBar.setVisibility(View.VISIBLE);
        new Thread(() -> {
            List<OfflineStory> offlineStories = db.offlineStoryDao().getAllStories();
            List<Story> stories = new ArrayList<>();
            Gson gson = new Gson();
            for (OfflineStory offlineStory : offlineStories) {
                stories.add(gson.fromJson(offlineStory.getStoryJson(), Story.class));
            }
            runOnUiThread(() -> {
                progressBar.setVisibility(View.GONE);
                if(stories.isEmpty()){
                    Toast.makeText(this, "No stories downloaded for offline use.", Toast.LENGTH_LONG).show();
                }
                setupRecyclerView(stories);
            });
        }).start();
    }

    private void setupRecyclerView(List<Story> stories) {
        storyAdapter = new StoryAdapter(this, stories, story -> {
            Intent intent = new Intent(MainActivity.this, StoryDetailActivity.class);
            intent.putExtra("STORY_ID", story.getId());
            // Pass a flag indicating whether we are in offline mode
            intent.putExtra("IS_OFFLINE", !isNetworkAvailable());
            startActivity(intent);
        });
        recyclerViewStories.setAdapter(storyAdapter);
    }
}