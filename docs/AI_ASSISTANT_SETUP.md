# AI Assistant Setup Guide

## Current Configuration: Ollama (Local)

The Track Collection AI Assistant is configured to use **Ollama** with the **llama3.1:8b** model running locally on your machine.

### Ollama Setup

1. **Make sure Ollama is running:**
   ```bash
   ollama serve
   ```
   (Usually auto-starts after installation)

2. **Pull the llama3.1:8b model:**
   ```bash
   ollama pull llama3.1:8b
   ```

3. **Start chatting!** No API key needed.

4. **Verify Ollama is working:**
   ```bash
   ollama list  # Check installed models
   ```

---

## Switching to Other AI Providers

Edit `app/Agents/TrackCollectionAgent.php` and update the provider configuration:

### Option 1: OpenAI (GPT-4)

```php
protected string $provider = 'default';
protected string $model = 'gpt-4o';
```

**Environment Setup:**
```env
# Add to .env
OPENAI_API_KEY=sk-your-key-here
```

### Option 2: Anthropic Claude

```php
protected string $provider = 'claude';
protected string $model = 'claude-3-7-sonnet-latest';
```

**Environment Setup:**
```env
# Add to .env
ANTHROPIC_API_KEY=sk-ant-your-key-here
```

### Option 3: Groq (Fast Inference)

```php
protected string $provider = 'groq';
protected string $model = 'llama-3.1-70b-versatile';
```

**Environment Setup:**
```env
# Add to .env
GROQ_API_KEY=your-groq-key-here
```

### Option 4: Google Gemini

```php
protected string $provider = 'gemini';
protected string $model = 'gemini-pro';
```

**Environment Setup:**
```env
# Add to .env
GEMINI_API_KEY=your-gemini-key-here
```

---

## Available AI Features

The AI assistant can help with:

- **List Tracks**: View all available tracks with detailed metadata (location, type, tags)
- **View Collections**: See existing track collections
- **Suggest Combinations**: Get AI recommendations for balanced track collections
- **Answer Questions**: Ask about tracks, collections, and game modes
- **Search & Filter**: Find specific tracks by criteria

---

## MCP Tools Available

The AI uses these MCP tools to access your data:

1. **ListTracks** - Returns all tracks with metadata (location, variant, type, tags, weather)
2. **GetTrackCollections** - Retrieves track collections by ID or name

---

## Troubleshooting

### "Cannot connect to Ollama"
- Verify Ollama is running: `ollama list`
- Check the default URL is correct: `http://localhost:11434`
- Restart Ollama service if needed

### "Model not found"
- Pull the model: `ollama pull llama3.1:8b`
- Check available models: `ollama list`

### "Slow responses"
- llama3.1:8b should be fast on most hardware
- Consider using a smaller model like `llama3.1:3b` for faster responses
- Or switch to a cloud provider (OpenAI, Claude) for better performance

### "AI not calling MCP tools"
- This is model-dependent - llama3.1 has function calling support
- If issues persist, try a larger model (llama3.1:70b) or switch to GPT-4/Claude

---

## Performance Recommendations

| Model | Speed | Quality | Hardware | Cost |
|-------|-------|---------|----------|------|
| llama3.1:8b (Ollama) | Fast | Good | 8GB+ RAM | Free |
| llama3.1:70b (Ollama) | Slow | Excellent | 32GB+ RAM | Free |
| GPT-4o (OpenAI) | Fast | Excellent | N/A | ~$0.01/query |
| Claude Sonnet (Anthropic) | Fast | Excellent | N/A | ~$0.01/query |
| Groq | Very Fast | Good | N/A | Free (limited) |

---

## Configuration File Reference

All AI provider settings are in: `config/laragent.php`

Agent configuration: `app/Agents/TrackCollectionAgent.php`

MCP server configuration: `config/laragent.php` (mcp_servers section)
