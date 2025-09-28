package com.storyapp.kids.models;

import com.google.gson.annotations.SerializedName;

public class Audio {
    @SerializedName("audio_url")
    private String audioUrl;

    @SerializedName("audio_file")
    private String audioFile;

    @SerializedName("use_tts")
    private boolean useTts;

    // Getters
    public String getAudioUrl() {
        return audioUrl;
    }

    public String getAudioFile() {
        return audioFile;
    }

    public boolean isUseTts() {
        return useTts;
    }

    // Setters
    public void setAudioUrl(String audioUrl) {
        this.audioUrl = audioUrl;
    }

    public void setAudioFile(String audioFile) {
        this.audioFile = audioFile;
    }

    public void setUseTts(boolean useTts) {
        this.useTts = useTts;
    }
}