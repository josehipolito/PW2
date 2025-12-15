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
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(<HomeButtons />);
