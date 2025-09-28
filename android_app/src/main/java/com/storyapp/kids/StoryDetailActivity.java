package com.storyapp.kids;

import android.media.MediaPlayer;
import android.os.Bundle;
import android.speech.tts.TextToSpeech;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.viewpager2.widget.ViewPager2;

import com.google.gson.Gson;
import com.storyapp.kids.db.AppDatabase;
import com.storyapp.kids.db.OfflineStory;
import com.storyapp.kids.models.Story;
import com.storyapp.kids.models.StoryPage;
import com.storyapp.kids.network.ApiClient;
import com.storyapp.kids.network.ApiService;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Locale;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class StoryDetailActivity extends AppCompatActivity {

    private static final String TAG = "StoryDetailActivity";
    private ViewPager2 viewPager;
    private StoryPageAdapter pageAdapter;
    private ProgressBar progressBar;
    private TextToSpeech tts;
    private MediaPlayer mediaPlayer;
    private Story currentStory;
    private Button playButton, stopButton, downloadButton;
    private AppDatabase db;
    private boolean isOffline;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_story_detail);

        int storyId = getIntent().getIntExtra("STORY_ID", -1);
        isOffline = getIntent().getBooleanExtra("IS_OFFLINE", false);

        Toolbar toolbar = findViewById(R.id.toolbar_detail);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        viewPager = findViewById(R.id.view_pager_story);
        progressBar = findViewById(R.id.progressBar_detail);
        playButton = findViewById(R.id.button_play_audio);
        stopButton = findViewById(R.id.button_stop_audio);
        downloadButton = findViewById(R.id.button_download);

        db = AppDatabase.getDatabase(getApplicationContext());

        if (isOffline) {
            downloadButton.setVisibility(View.GONE);
        }

        initializeTts();
        initializeMediaPlayer();

        if (storyId != -1) {
            if (isOffline) {
                fetchStoryDetailsFromDb(storyId);
            } else {
                fetchStoryDetails(storyId);
                checkIfStoryIsDownloaded(storyId);
            }
        } else {
            Toast.makeText(this, "Story not found.", Toast.LENGTH_SHORT).show();
            finish();
        }

        playButton.setOnClickListener(v -> playAudioForCurrentPage());
        stopButton.setOnClickListener(v -> stopAudio());
        downloadButton.setOnClickListener(v -> downloadStoryWithMedia());
    }

    private void checkIfStoryIsDownloaded(int storyId) {
        new Thread(() -> {
            OfflineStory offlineStory = db.offlineStoryDao().getStoryById(storyId);
            if (offlineStory != null) {
                runOnUiThread(() -> {
                    downloadButton.setText("Downloaded");
                    downloadButton.setEnabled(false);
                });
            }
        }).start();
    }

    private void downloadStoryWithMedia() {
        if (currentStory == null) {
            Toast.makeText(this, "Story data not loaded yet.", Toast.LENGTH_SHORT).show();
            return;
        }

        Toast.makeText(this, "Starting download... This may take a moment.", Toast.LENGTH_SHORT).show();
        downloadButton.setEnabled(false);
        progressBar.setVisibility(View.VISIBLE);

        new Thread(() -> {
            try {
                Gson gson = new Gson();
                Story storyToSave = gson.fromJson(gson.toJson(currentStory), Story.class);

                // Download cover image
                String coverImageUrl = storyToSave.getCoverImage();
                if (coverImageUrl != null && !coverImageUrl.isEmpty()) {
                    File localFile = downloadFile(coverImageUrl);
                    if (localFile != null) storyToSave.setCoverImage(localFile.getAbsolutePath());
                }

                // Download page images and audio
                for (StoryPage page : storyToSave.getPages()) {
                    String pageImageUrl = page.getImage();
                    if (pageImageUrl != null && !pageImageUrl.isEmpty()) {
                        File localFile = downloadFile(pageImageUrl);
                        if (localFile != null) page.setImage(localFile.getAbsolutePath());
                    }
                    if (page.getAudio() != null) {
                        String audioUrl = page.getAudio().getAudioUrl();
                        if (audioUrl != null && !audioUrl.isEmpty() && audioUrl.startsWith("http")) {
                            File localFile = downloadFile(audioUrl);
                            if (localFile != null) page.getAudio().setAudioUrl(localFile.getAbsolutePath());
                        }
                        String audioFileUrl = page.getAudio().getAudioFile();
                         if (audioFileUrl != null && !audioFileUrl.isEmpty() && audioFileUrl.startsWith("http")) {
                            File localFile = downloadFile(audioFileUrl);
                            if (localFile != null) page.getAudio().setAudioFile(localFile.getAbsolutePath());
                        }
                    }
                }

                // Save the modified story object to the database
                String storyJson = gson.toJson(storyToSave);
                OfflineStory offlineStory = new OfflineStory();
                offlineStory.setId(storyToSave.getId());
                offlineStory.setStoryJson(storyJson);
                db.offlineStoryDao().insert(offlineStory);

                runOnUiThread(() -> {
                    progressBar.setVisibility(View.GONE);
                    Toast.makeText(StoryDetailActivity.this, "Story downloaded successfully!", Toast.LENGTH_LONG).show();
                    downloadButton.setText("Downloaded");
                });

            } catch (Exception e) {
                Log.e(TAG, "Download failed", e);
                runOnUiThread(() -> {
                    progressBar.setVisibility(View.GONE);
                    Toast.makeText(StoryDetailActivity.this, "Download failed.", Toast.LENGTH_SHORT).show();
                    downloadButton.setEnabled(true);
                });
            }
        }).start();
    }

    private File downloadFile(String fileUrl) throws IOException {
        URL url = new URL(fileUrl);
        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.connect();

        if (connection.getResponseCode() != HttpURLConnection.HTTP_OK) {
            Log.e(TAG, "Server returned HTTP " + connection.getResponseCode() + " for URL: " + fileUrl);
            return null;
        }

        String fileName = fileUrl.substring(fileUrl.lastIndexOf('/') + 1);
        File mediaDir = new File(getExternalFilesDir(null), "media");
        if (!mediaDir.exists()) {
            mediaDir.mkdirs();
        }

        File file = new File(mediaDir, fileName);
        try (InputStream input = connection.getInputStream();
             FileOutputStream output = new FileOutputStream(file)) {
            byte[] data = new byte[4096];
            int count;
            while ((count = input.read(data)) != -1) {
                output.write(data, 0, count);
            }
        }
        return file;
    }

    private void initializeTts() {
        tts = new TextToSpeech(this, status -> {
            if (status == TextToSpeech.SUCCESS) {
                int result = tts.setLanguage(Locale.US);
                if (result == TextToSpeech.LANG_MISSING_DATA || result == TextToSpeech.LANG_NOT_SUPPORTED) {
                    Log.e(TAG, "TTS language not supported.");
                }
            } else {
                Log.e(TAG, "TTS initialization failed.");
            }
        });
    }

    private void initializeMediaPlayer() {
        mediaPlayer = new MediaPlayer();
    }

    private void fetchStoryDetails(int storyId) {
        progressBar.setVisibility(View.VISIBLE);
        ApiClient.getApiService().getStoryDetails(storyId).enqueue(new Callback<Story>() {
            @Override
            public void onResponse(Call<Story> call, Response<Story> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    currentStory = response.body();
                    setupViewPager();
                    getSupportActionBar().setTitle(currentStory.getTitle());
                } else {
                    Toast.makeText(StoryDetailActivity.this, "Failed to load story details.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Story> call, Throwable t) {
                progressBar.setVisibility(View.GONE);
                Log.e(TAG, "API call failed", t);
                Toast.makeText(StoryDetailActivity.this, "An error occurred.", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void fetchStoryDetailsFromDb(int storyId) {
        progressBar.setVisibility(View.VISIBLE);
        new Thread(() -> {
            OfflineStory offlineStory = db.offlineStoryDao().getStoryById(storyId);
            if (offlineStory != null) {
                Gson gson = new Gson();
                currentStory = gson.fromJson(offlineStory.getStoryJson(), Story.class);
                runOnUiThread(() -> {
                    progressBar.setVisibility(View.GONE);
                    setupViewPager();
                    getSupportActionBar().setTitle(currentStory.getTitle());
                });
            } else {
                 runOnUiThread(() -> {
                    progressBar.setVisibility(View.GONE);
                    Toast.makeText(StoryDetailActivity.this, "Error: Could not load story from device.", Toast.LENGTH_LONG).show();
                    finish();
                });
            }
        }).start();
    }

    private void setupViewPager() {
        pageAdapter = new StoryPageAdapter(this, currentStory.getPages());
        viewPager.setAdapter(pageAdapter);
    }

    private void playAudioForCurrentPage() {
        if (currentStory == null || currentStory.getPages().isEmpty()) return;

        stopAudio();

        int position = viewPager.getCurrentItem();
        StoryPage currentPage = currentStory.getPages().get(position);

        if (currentPage.getAudio() != null) {
            if (currentPage.getAudio().isUseTts()) {
                tts.speak(currentPage.getText(), TextToSpeech.QUEUE_FLUSH, null, null);
            } else {
                String path = currentPage.getAudio().getAudioUrl();
                if (path == null || path.isEmpty()) {
                    path = currentPage.getAudio().getAudioFile();
                }
                if(path != null && !path.isEmpty()){
                     playFromFilePath(path);
                }
            }
        }
    }

    private void playFromFilePath(String path) {
        try {
            mediaPlayer.reset();
            mediaPlayer.setDataSource(path);
            mediaPlayer.prepareAsync();
            mediaPlayer.setOnPreparedListener(mp -> mp.start());
        } catch (IOException e) {
            Log.e(TAG, "Error playing audio from path: " + path, e);
            Toast.makeText(this, "Error playing audio.", Toast.LENGTH_SHORT).show();
        }
    }

    private void stopAudio() {
        if (tts.isSpeaking()) {
            tts.stop();
        }
        if (mediaPlayer.isPlaying()) {
            mediaPlayer.stop();
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }

    @Override
    protected void onDestroy() {
        if (tts != null) {
            tts.stop();
            tts.shutdown();
        }
        if (mediaPlayer != null) {
            mediaPlayer.release();
            mediaPlayer = null;
        }
        super.onDestroy();
    }
}