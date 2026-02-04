function HomeButtons() {
  return (
    <div className="home-buttons">
      <a href="editar.php">
        <button className="button edit-button">
          ✏️ Editar Jogos e Resultados
        </button>
      </a>

      <a href="ver_jornadas.php">
        <button className="button view-button">
          📅 Ver Jornadas
        </button>
      </a>

      <a href="classificacao.php">
        <button className="button view-button">
          📊 Classificação
        </button>
      </a>

      <a href="ver_estatisticas.php">
        <button className="button view-button">
          📈 Estatísticas da Liga
        </button>
      </a>
      <a href="registar_estatisticas.php">
        <button className="button edit-button" style={{ background: 'linear-gradient(135deg, #ff9800, #f57c00)' }}>
          ⚽ Registar Estatísticas Individuais
        </button>
      </a>
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(<HomeButtons />);