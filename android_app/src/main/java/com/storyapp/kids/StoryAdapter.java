package com.storyapp.kids;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.storyapp.kids.models.Story;
import java.util.List;

public class StoryAdapter extends RecyclerView.Adapter<StoryAdapter.StoryViewHolder> {

    private Context context;
    private List<Story> stories;
    private OnStoryClickListener listener;

    public interface OnStoryClickListener {
        void onStoryClick(Story story);
    }

    public StoryAdapter(Context context, List<Story> stories, OnStoryClickListener listener) {
        this.context = context;
        this.stories = stories;
        this.listener = listener;
    }

    @NonNull
    @Override
    public StoryViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_story, parent, false);
        return new StoryViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull StoryViewHolder holder, int position) {
        Story story = stories.get(position);
        holder.bind(story, listener);
    }

    @Override
    public int getItemCount() {
        return stories.size();
    }

    class StoryViewHolder extends RecyclerView.ViewHolder {
        ImageView coverImage;
        TextView title;

        public StoryViewHolder(@NonNull View itemView) {
            super(itemView);
            coverImage = itemView.findViewById(R.id.imageView_cover);
            title = itemView.findViewById(R.id.textView_title);
        }

        public void bind(final Story story, final OnStoryClickListener listener) {
            title.setText(story.getTitle());

            Glide.with(context)
                .load(story.getCoverImage())
                .placeholder(R.color.pastel_green) // A placeholder color
                .into(coverImage);

            itemView.setOnClickListener(v -> listener.onStoryClick(story));
        }
    }
}