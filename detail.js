function getYouTubeEmbedUrl(url) {
  // Fungsi untuk mengubah link YouTube ke format embed
  const regExp = /(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/;
  const match = url.match(regExp);
  if (match && match[1]) {
    return `https://www.youtube.com/embed/${match[1]}`;
  }
  return null;
}